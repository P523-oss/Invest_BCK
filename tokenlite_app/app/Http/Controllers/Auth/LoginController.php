<?php

namespace App\Http\Controllers\Auth;


use Cookie;
use Carbon\Carbon;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Activity;
use App\Models\UserMeta;
use App\Models\Referral;
use App\Helpers\ReCaptcha;
use App\Helpers\IcoHandler;
use Jenssegers\Agent\Agent;
use App\Notifications\UnusualLogin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\UserRegistered;
use Illuminate\Http\Request as AuthRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers, ReCaptcha; //, ThrottlesLogins;
    protected $maxAttempts = 6; // Default is 5
    protected $decayMinutes = 15; // Default is 1

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $handler;
    public function __construct(IcoHandler $handler)
    {
        $this->handler = $handler;
        $this->middleware('guest')
            ->except(['logout', 'log-out', 'verified', 'registered', 'checkLoginState']);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request as AuthRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(AuthRequest $request)
    {
        if(recaptcha()) {
            $this->checkReCaptcha($request->recaptcha);
        }

        $this->validateLogin($request);
        $attempt = $this->hasTooManyLoginAttempts($request);

        if ($attempt) {
            $this->fireLockoutEvent($request);

            $email = $request->email;
            $user = User::where('email', $email)->first();

            $totalAttempts = $this->totalAttempts($request);
            if ($user && $totalAttempts < $this->maxAttempts) {
                $userMeta = UserMeta::where('userId', $user->id)->first();
                if ($userMeta->unusual == 1) {
                    try{
                        $user->notify(new UnusualLogin($user));
                    }catch(\Exception $e){
                    } finally{
                        $this->incrementLoginAttempts($request);
                    }
                }
            }

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Return how much time attempts to login
     *
     * @version 1.0.0
     * @param Illuminate\Http\Request as $request
     * @return integer
     */
    public function totalAttempts(AuthRequest $request)
    {
        return $this->limiter()->attempts($this->throttleKey($request));
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        if (!file_exists(storage_path('installed'))) {
            return redirect(url('/install'));
        }

        $have_user = User::where('role', 'admin')->count();
        if(!$have_user){
            return redirect(url('/register?setup=admin'));
        }
        return view('auth.login');
    }


    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request as AuthRequest $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendLockoutResponse(AuthRequest $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );
        $seconds = ($seconds >= 60 ? gmdate('i', $seconds).' minutes.' : $seconds.' seconds.');

        throw ValidationException::withMessages([
            $this->username() => [__('auth.throttle', ['seconds' => $seconds])],
        ])->status(429);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated()
    {
        $user = Auth::user();
        $check = str_contains(app_key(), $this->handler->find_the_path($this->handler->getDomain())) && $this->handler->cris_cros($this->handler->getDomain(), app_key(2));
        if( !$user->is('admin') && !$check ){
            Auth::logout();
            return redirect()->route('login')->with([
                'warning' => $this->handler->accessMessage()
            ]);
        }
        $user->lastLogin = now();
        if($user->is('admin')) { $user->generateSecret(); }
        $user->save();
        if (UserMeta::getMeta(Auth::id())->save_activity == 'TRUE') {
            $agent = new Agent();
            $ret['activity'] = Activity::create([
                'user_id' => Auth::id(),
                'browser' => $agent->browser() . '/' . $agent->version($agent->browser()),
                'device' => $agent->device() . '/' . $agent->platform() . '-' . $agent->version($agent->platform()),
                'ip' => request()->ip(),
            ]);
        }
        if (!$user->selfCertified()){
            return redirect()->route('user.selfcertify');
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function checkLoginState(AuthRequest $request)
    {
        // -->FROM CENTRAL LOGIN
        $auth_user;
        $fromCentral = false;

        if(isset($_COOKIE['name'])) {
            $auth_user = json_decode($_COOKIE['name']);
            $fromCentral = true;
        }
        // <--FROM CENTRAL LOGIN
        
        if (application_installed(true) == false) {
            return redirect(url('/install'));
        }

        // if(Auth::check()){
        //     $user = Auth::user();
        //     if ($user->status == 'active') {
        //         $link = ($user->role == 'admin' || $user->role == 'supervisor') ? '/admin' : '/user';
        //         return redirect(url('/') . $link);
        //     } else {
        //         Auth::logout();
        //         // return redirect(route('login'))->with(['danger' => __('messages.login.inactive')]);
        //         return redirect(route('auth0login'));
        //     }
        // }else if($request->session()->has('auth0__user')){
        //     $email = $request->session()->get('auth0__user')['email'];
        //     $user = User::where('email', $email)->first();
        //     // add condition if user from auth0 does not exist in user tbl
        //     if ($user === null) {
        //         /*
        //           ***user doesn't exist
        //           ***auto create user
        //         */
        //         $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //         $token = $request->session()->get('auth0__user');
        //         $generatePassword = substr(str_shuffle($permitted_chars), 0, 60);
        //         $newUser = User::create([
        //             'name' => $token['name'],
        //             'email' => $token['email'],
        //             'email_verified_at' => Carbon::now()->toDateTimeString(),
        //             'password' =>  $generatePassword,
        //             'registerMethod' => 'Auth0',
        //             'lastLogin' => Carbon::now()->toDateTimeString()
        //         ]);
        //         /*
        //             if user uses referral
        //         */ 
        //         if(is_active_referral_system()) {
        //             if (Cookie::has('ico_nio_ref_by')) {
        //                 $ref_id = (int) Cookie::get('ico_nio_ref_by');
        //                 $ref_user = User::where('id', $ref_id)->where('email_verified_at', '!=', null)->first();
        //                 if ($ref_user) {
        //                     $newUser->referral = $ref_user->id;
        //                     $newUser->referralInfo = json_encode([
        //                         'user' => $ref_user->id,
        //                         'name' => $ref_user->name,
        //                         'time' => now(),
        //                     ]);
        //                     $refer_blank = false;
        //                     $this->create_referral_or_not($newUser->id, $ref_user->id);
        //                     Cookie::queue(Cookie::forget('ico_nio_ref_by'));
        //                 }
        //             }
        //         }
        //         // **END REFERRAL 
        //         Auth::login($newUser);
        //         $link = ($newUser->role == 'admin' || $newUser->role == 'supervisor') ? '/admin' : '/user';
        //         /*
        //          ** Mailing Registered Users
        //         */
        //         $newUser->notify(new UserRegistered($newUser));
        //         return redirect(url('/') . $link);
        //     }else{
        //         Auth::login($user);
        //         $link = ($user->role == 'admin' || $user->role == 'supervisor') ? '/admin' : '/user';
        //         return redirect(url('/') . $link);
        //     }
        // }else{
        //     // return redirect(url('/login'));
        //     Auth::logout();
        //     session()->forget('_g2fa_session');
        //     return redirect(url('/login'));
        //     // return redirect(url('/auth0login'));
        // }
        

        if(Auth::check()){
            $user = Auth::user();
            if ($user->status == 'active') {
                $link = ($user->role == 'admin' || $user->role == 'supervisor') ? '/admin' : '/user';
                return redirect(url('/') . $link);
            } else {
                Auth::logout();
                return redirect(url('/login'));
            }
        }else if($fromCentral){
           $validateUser =  $this->validateUser($auth_user);
           $link = ($validateUser->role == 'admin' || $validateUser->role == 'supervisor') ? '/admin' : '/user';
           Auth::login($validateUser);
           return redirect(url('/') . $link);
           
        }else{
            Auth::logout();
            session()->forget('_g2fa_session');
            return redirect(url('/login'));
        }
        
    }

    /**
     * CENTRAL LOGIN USER
    */
    public function validateUser($request){
        $email = $request->email;
            $user = User::where('email', $email)->first();
            // add condition if user from auth0 does not exist in user tbl
            if ($user === null) {
                /*
                  ***user doesn't exist
                  ***auto create user
                */
                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $token = $request;

                $generatePassword = substr(str_shuffle($permitted_chars), 0, 60);
                $newUser = User::create([
                    'name' => $token->name,
                    'email' => $token->email,
                    'email_verified_at' => Carbon::now()->toDateTimeString(),
                    'password' =>  $generatePassword,
                    'registerMethod' => 'Auth0',
                    'lastLogin' => Carbon::now()->toDateTimeString()
                ]);
                /*
                    if user uses referral
                */ 
                if(is_active_referral_system()) {
                    if (Cookie::has('ico_nio_ref_by')) {
                        $ref_id = (int) Cookie::get('ico_nio_ref_by');
                        $ref_user = User::where('id', $ref_id)->where('email_verified_at', '!=', null)->first();
                        if ($ref_user) {
                            $newUser->referral = $ref_user->id;
                            $newUser->referralInfo = json_encode([
                                'user' => $ref_user->id,
                                'name' => $ref_user->name,
                                'time' => now(),
                            ]);
                            $refer_blank = false;
                            $this->create_referral_or_not($newUser->id, $ref_user->id);
                            Cookie::queue(Cookie::forget('ico_nio_ref_by'));
                        }
                    }
                }
                // **END REFERRAL 
                // Auth::login($newUser);
                $link = ($newUser->role == 'admin' || $newUser->role == 'supervisor') ? '/admin' : '/user';
                /*
                 ** Mailing Registered Users
                */
                $newUser->notify(new UserRegistered($newUser));
                // return redirect(url('/') . $link);
                return $newUser;
            }else{
                // Auth::login($user);
                $link = ($user->role == 'admin' || $user->role == 'supervisor') ? '/admin' : '/user';
                // return redirect(url('/') . $link);
                return $user;
            }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        Auth::logout();
        session()->forget('_g2fa_session');
        // return redirect(url('/login'));
        $central_url = env('CENTRAL_LOGIN');
        return redirect($central_url.'logout');
        // return redirect(route('auth0logout'));
        
        // return (!is_maintenance() ? redirect(route('login')) : redirect(route('admin.login')));
    }


    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function loggedOut(Request $request)
    {
        session()->forget('_g2fa_session');
        return (! is_maintenance() ? redirect(route('login')) : redirect(route('admin.login')));
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function verified()
    {
        Auth::logout();
        return view('auth.message')->with(['text' => __('messages.verify.success.heading'), 'subtext' => __('messages.verify.success.subhead'), 'msg' => __('messages.verify.success.msg')]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */

    public function registered()
    {
        Auth::logout();
        $data = ['text' => __('messages.register.success.heading'), 'subtext' => __('messages.register.success.subhead'), 'msg' => ['type' => 'success', 'text' => __('messages.register.success.msg')]];
        $last_url = str_replace(url('/'), '', url()->previous());
        if ($last_url == '/register') {
            return view('auth.message')->with($data);
        }
        return redirect('/login');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request as AuthRequest $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(AuthRequest $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [__('auth.failed')],
        ]);
    }

    /*
        create referral
    */ 
    protected function create_referral_or_not($user, $refer=0) {
        Referral::create([ 'user_id' => $user, 'user_bonus' => 0, 'refer_by' => $refer, 'refer_bonus' => 0 ]);
    }

}
