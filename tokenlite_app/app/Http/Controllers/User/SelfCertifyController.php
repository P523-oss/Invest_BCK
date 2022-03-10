<?php

namespace App\Http\Controllers\User;
/**
 * Register Controller
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.1.2
 */

use App\Models\SelfCert;
use Cookie;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserType;
use App\Models\Country;
use App\Models\Referral;
use App\Models\UserMeta;
use App\Helpers\ReCaptcha;
use App\Helpers\IcoHandler;
use Illuminate\Http\Request;
use App\Notifications\ConfirmEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;

class SelfCertifyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SelfCertify Controller
    |--------------------------------------------------------------------------
    |
    | After registering user should add more fields and self certify.
    |
     */

    use RegistersUsers, ReCaptcha;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     * @version 1.0.0
     */
    protected $redirectTo = '/register/success';

    /**
     * Create a new controller instance.
     *
     * @version 1.0.0
     * @return void
     */
    protected $handler;
    public function __construct(IcoHandler $handler)
    {
        $this->handler = $handler;
        //$this->middleware('guest');
    }

    public function selfcertifyForm()
    {
        return view('user.selfcertify1');
    }

    /**
     * Handle a registration request for the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function selfcertify()
    {
        $user = Auth::user();
        if($user->self_cert_form=='complete'){
            return view('user.selfcertifyok');
        }
        if (application_installed(true) == false) {
            return redirect(url('/install'));
        }
        if(recaptcha()) {
            $this->checkReCaptcha($request->recaptcha);
        }
        $user = Auth::user();
        $have_user = User::where('role', 'admin')->count();
        if( $have_user >= 1 && ! $this->handler->check_body() ){
            return back()->withInput()->with([
                'warning' => $this->handler->accessMessage()
            ]);
        }
        $name=$user->name;
        $email=$user->email;
        $password=$user->password;
        $user_type_id = $user->user_type_id?? 1;
        $message = UserType::find($user_type_id)->first()->message;
        $userTypeDescription = UserType::find($user_type_id)->first()->description;

        $nextform = 'user.selfcertify1';
        switch ($user_type_id){
            case '1':  $nextform = 'user.selfcertify1'; break;
            case '2':  $nextform = 'user.selfcertify2'; break;
            case '3':  $nextform = 'user.selfcertify3'; break;
            case '4':  $nextform = 'user.selfcertify4'; break;
        }

        //event(new Registered($user = $this->create($request->all())));
        //$this->guard()->login($user);
        $countries = Country::get();
        return view($nextform, compact('name', 'email','password', 'user_type_id', 'userTypeDescription', 'message','countries'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function selfcertify1(Request $request)
    {
        $this->validator($request->all())->validate();
        $id = Auth::id();
        if (isset($request['qualified1'])){
            $q1 = 1;
        }
        else{
            $q1 = 0;
        }
        if (isset($request['qualified2'])){
            $q2 = 1;
        }
        else{
            $q2 = 0;
        }
        if (isset($request['qualified3'])){
            $q3 = 1;
        }
        else{
            $q3 = 0;
        }
        if (isset($request['qualified4'])){
            $q4 = 1;
        }
        else{
            $q4 = 0;
        }
        if (isset($request['qualified5'])){
            $q5 = 1;
        }
        else{
            $q5 = 0;
        }
       
        $user1 = SelfCert::create([
            'user_id' => $id,
            'accredited' => $request->input('accredited'),
            'qualified1' => $q1,
            'qualified2' => $q2,
            'qualified3' => $q3,
            'qualified4' => $q4,
            'qualified5' => $q5,
            'annual_income' => $request->input('annual_income'),
            'net_worth' => $request->input('net_worth'),
            'us_citizen' => $request->input('us'),
            'backup' => $request->input('backup'),
        ]);

        if($user1){
            $user1 = User::FindOrFail(Auth::id());
            $user1->self_cert_form = 'complete';
            $user1->save();
        }

        $user = User::findOrFail(Auth::user()->id);

        $user->country_residence= $request['country_residence'];
        $user->country_citizenship= $request['country_citizenship'];
        $user->address= $request['address'];
        $user->city= $request['city'];
        $user->state= $request['state'];
        $user->postalcode= $request['postalcode'];
        $user->selfCertifyStatus= "self certified";
        $user->update();
        /*
        ** Mailing Self Certified
        */
        // $mail = new \App\Http\Controllers\SparkPostController;
        // $sendEmail = $mail->sendMail('selfcert',$user->name,$user->email);
        return view('user.selfcertifyok');

    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @version 1.0.1
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $term = get_page('terms', 'status') == 'active' ? 'required' : 'nullable';
        return Validator::make($data, [
            'country_residence' => ['required'],
            'country_citizenship' => ['required'],
            'address' => ['required'],
            'city' => ['required'],
            'state' => ['required'],
            'postalcode' => ['required']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @version 1.2.1
     * @since 1.0.0
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $have_user = User::where('role', 'admin')->count();
        $type = ($have_user >= 1) ? 'user' : 'admin';
        $email_verified = ($have_user >= 1) ? null : now();
        $user = User::create([
            'name' => strip_tags($data['name']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'lastLogin' => date('Y-m-d H:i:s'),
            'role' => $type,
        ]);
        if ($user) {
            if ($have_user <= 0) {
                save_gmeta('site_super_admin', 1, $user->id);
            }
            $user->email_verified_at = $email_verified;
            $refer_blank = true;
            if(is_active_referral_system()) {
                if (Cookie::has('ico_nio_ref_by')) {
                    $ref_id = (int) Cookie::get('ico_nio_ref_by');
                    $ref_user = User::where('id', $ref_id)->where('email_verified_at', '!=', null)->first();
                    if ($ref_user) {
                        $user->referral = $ref_user->id;
                        $user->referralInfo = json_encode([
                            'user' => $ref_user->id,
                            'name' => $ref_user->name,
                            'time' => now(),
                        ]);
                        $refer_blank = false;
                        $this->create_referral_or_not($user->id, $ref_user->id);
                        Cookie::queue(Cookie::forget('ico_nio_ref_by'));
                    }
                }
            }
            if($user->role=='user' && $refer_blank==true) {
                $this->create_referral_or_not($user->id);
            }

            $user->save();
            $meta = UserMeta::create([ 'userId' => $user->id ]);

            $meta->notify_admin = ($type=='user')?0:1;
            $meta->email_token = str_random(65);
            $cd = Carbon::now(); //->toDateTimeString();
            $meta->email_expire = $cd->copy()->addMinutes(75);
            $meta->save();

            if ($user->email_verified_at == null) {
                try {
                    $user->notify(new ConfirmEmail($user));
                } catch (\Exception $e) {
                    session('warning', 'User registered successfully, but we unable to send confirmation email!');
                }
            }
        }
        return $user;
    }

    /**
     * Create user in referral table.
     *
     * @param  $user, $refer
     * @version 1.0
     * @since 1.1.2
     * @return \App\Models\User
     */
    protected function create_referral_or_not($user, $refer=0) {
        Referral::create([ 'user_id' => $user, 'user_bonus' => 0, 'refer_by' => $refer, 'refer_bonus' => 0 ]);
    }
}
