<?php

namespace App\Http\Controllers\User;

/**
 * User Controller
 *
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0.6
 */
use Auth;
use Validator;
use IcoHandler;
use Carbon\Carbon;
use App\Models\Page;
use App\Models\User;
use App\Models\IcoStage;
use App\Models\UserMeta;
use App\Models\Activity;
use App\Helpers\NioModule;
use App\Models\GlobalMeta;
use App\Models\Transaction;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use App\Notifications\PasswordChange;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\SelfCert;

class UserController extends Controller
{
    protected $handler;
    public function __construct(IcoHandler $handler)
    {
        $this->middleware('auth');
        $this->handler = $handler;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function index()
    {
        if( !$this->handler->check_body() && empty(app_key()) ){
            Auth::logout();
            return redirect()->route('login')->with([
                'warning' => $this->handler->accessMessage()
            ]);
        }
        $user = Auth::user();

        $stage = $active_bonus = $contribution = null;
        if (session()->has('currentsymbol')) {
            $stage = active_stage();
            $contribution = Transaction::user_contribution();
            $tc = new \App\Helpers\TokenCalculate();
            $active_bonus = $tc->get_current_bonus('active');
        }
        return view('user.dashboard', compact('user', 'stage', 'active_bonus', 'contribution'));
    }


    /**
     * Show the user account page.
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function account()
    {
        $countries = $this->handler->getCountries();
        $user = Auth::user();
        $userMeta = UserMeta::getMeta($user->id);

        $g2fa = new Google2FA();
        $google2fa_secret = $g2fa->generateSecretKey();
        $google2fa = $g2fa->getQRCodeUrl(
            site_info().'-'.$user->name,
            $user->email,
            $google2fa_secret
        );

        return view('user.account', compact('user', 'userMeta','countries', 'google2fa', 'google2fa_secret'));
    }

    /**
     * Show the user account activity page.
     * and Delete Activity
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function account_activity()
    {
        $user = Auth::user();
        $activities = Activity::where('user_id', $user->id)->orderBy('created_at', 'DESC')->limit(50)->get();

        return view('user.activity', compact('user', 'activities'));
    }

    /**
     * Show the user account token management page.
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.1.2
     * @return void
     */
    public function mytoken_balance()
    {
        if(gws('user_mytoken_page')!=1) {
            return redirect()->route('user.home');
        }
        $user = Auth::user();
        $token_account = Transaction::user_mytoken('balance');
        $token_stages = Transaction::user_mytoken('stages');
        $user_modules = nio_module()->user_modules();
        return view('user.account-token', compact('user', 'token_account', 'token_stages', 'user_modules'));
    }

    /**
     * Activity delete
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function account_activity_delete(Request $request)
    {
        $id = $request->input('delete_activity');
        $ret['msg'] = 'info';
        $ret['message'] = "Nothing to do!";

        if ($id !== 'all') {
            $remove = Activity::where('id', $id)->where('user_id', Auth::id())->delete();
        } else {
            $remove = Activity::where('user_id', Auth::id())->delete();
        }
        if ($remove) {
            $ret['msg'] = 'success';
            $ret['message'] = __('messages.delete.delete', ['what'=>'Activity']);
        } else {
            $ret['msg'] = 'warning';
            $ret['message'] = __('messages.form.wrong');
        }
        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }

    /**
     * update the user account page.
     *
     * @return \Illuminate\Http\Response
     * @version 1.2
     * @since 1.0
     * @return void
     */
    public function account_update(Request $request)
    {
        $type = $request->input('action_type');
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');

        if ($type == 'personal_data') {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:3',
                'email' => 'required|email',
                'dateOfBirth' => 'required|date_format:"m/d/Y"'
            ]);

            if ($validator->fails()) {
                $msg = __('messages.form.wrong');
                if ($validator->errors()->hasAny(['name', 'email', 'dateOfBirth'])) {
                    $msg = $validator->errors()->first();
                }

                $ret['msg'] = 'warning';
                $ret['message'] = $msg;
                return response()->json($ret);
            } else {
                $user = User::FindOrFail(Auth::id());
                $user->name = strip_tags($request->input('name'));
                $user->email = $request->input('email');
                $user->mobile = strip_tags($request->input('mobile'));
                $user->dateOfBirth = $request->input('dateOfBirth');
                $user->nationality = strip_tags($request->input('nationality'));
                $user_saved = $user->save();

                if ($user) {
                    $ret['msg'] = 'success';
                    $ret['message'] = __('messages.update.success', ['what' => 'Account']);
                } else {
                    $ret['msg'] = 'warning';
                    $ret['message'] = __('messages.update.warning');
                }
            }
        }
        if ($type == 'wallet') {
            $validator = Validator::make($request->all(), [
                'wallet_name' => 'required',
                'wallet_address' => 'required|min:10'
            ]);

            if ($validator->fails()) {
                $msg = __('messages.form.wrong');
                if ($validator->errors()->hasAny(['wallet_name', 'wallet_address'])) {
                    $msg = $validator->errors()->first();
                }

                $ret['msg'] = 'warning';
                $ret['message'] = $msg;
                return response()->json($ret);
            } else {
                $is_valid = $this->handler->validate_address($request->input('wallet_address'), $request->input('wallet_name'));
                if ($is_valid) {
                    $user = User::FindOrFail(Auth::id());
                    $user->walletType = $request->input('wallet_name');
                    $user->walletAddress = $request->input('wallet_address');
                    $user_saved = $user->save();

                    if ($user) {
                        $ret['msg'] = 'success';
                        $ret['message'] = __('messages.update.success', ['what' => 'Wallet']);
                    } else {
                        $ret['msg'] = 'warning';
                        $ret['message'] = __('messages.update.warning');
                    }
                } else {
                    $ret['msg'] = 'warning';
                    $ret['message'] = __('messages.invalid.address');
                }
            }
        }
        if ($type == 'wallet_request') {
            $validator = Validator::make($request->all(), [
                'wallet_name' => 'required',
                'wallet_address' => 'required|min:10'
            ]);

            if ($validator->fails()) {
                $msg = __('messages.form.wrong');
                if ($validator->errors()->hasAny(['wallet_name', 'wallet_address'])) {
                    $msg = $validator->errors()->first();
                }

                $ret['msg'] = 'warning';
                $ret['message'] = $msg;
                return response()->json($ret);
            } else {
                $is_valid = $this->handler->validate_address($request->input('wallet_address'), $request->input('wallet_name'));
                if ($is_valid) {
                    $meta_data = ['name' => $request->input('wallet_name'), 'address' => $request->input('wallet_address')];
                    $meta_request = GlobalMeta::save_meta('user_wallet_address_change_request', json_encode($meta_data), auth()->id());

                    if ($meta_request) {
                        $ret['msg'] = 'success';
                        $ret['message'] = __('messages.wallet.change');
                    } else {
                        $ret['msg'] = 'warning';
                        $ret['message'] = __('messages.wallet.failed');
                    }
                } else {
                    $ret['msg'] = 'warning';
                    $ret['message'] = __('messages.invalid.address');
                }
            }
        }
        if ($type == 'notification') {
            $notify_admin = $newsletter = $unusual = 0;

            if (isset($request['notify_admin'])) {
                $notify_admin = 1;
            }
            if (isset($request['newsletter'])) {
                $newsletter = 1;
            }
            if (isset($request['unusual'])) {
                $unusual = 1;
            }

            $user = User::FindOrFail(Auth::id());
            if ($user) {
                $userMeta = UserMeta::where('userId', $user->id)->first();
                if ($userMeta == null) {
                    $userMeta = new UserMeta();
                    $userMeta->userId = $user->id;
                }
                $userMeta->notify_admin = $notify_admin;
                $userMeta->newsletter = $newsletter;
                $userMeta->unusual = $unusual;
                $userMeta->save();
                $ret['msg'] = 'success';
                $ret['message'] = __('messages.update.success', ['what' => 'Notification']);
            } else {
                $ret['msg'] = 'warning';
                $ret['message'] = __('messages.update.warning');
            }
        }
        if ($type == 'security') {
            $save_activity = $mail_pwd = 'FALSE';

            if (isset($request['save_activity'])) {
                $save_activity = 'TRUE';
            }
            if (isset($request['mail_pwd'])) {
                $mail_pwd = 'TRUE';
            }

            $user = User::FindOrFail(Auth::id());
            if ($user) {
                $userMeta = UserMeta::where('userId', $user->id)->first();
                if ($userMeta == null) {
                    $userMeta = new UserMeta();
                    $userMeta->userId = $user->id;
                }
                $userMeta->pwd_chng = $mail_pwd;
                $userMeta->save_activity = $save_activity;
                $userMeta->save();
                $ret['msg'] = 'success';
                $ret['message'] = __('messages.update.success', ['what' => 'Security']);
            } else {
                $ret['msg'] = 'warning';
                $ret['message'] = __('messages.update.warning');
            }
        }
        if ($type == 'account_setting') {
            $notify_admin = $newsletter = $unusual = 0;
            $save_activity = $mail_pwd = 'FALSE';
            $user = User::FindOrFail(Auth::id());

            if (isset($request['save_activity'])) {
                $save_activity = 'TRUE';
            }
            if (isset($request['mail_pwd'])) {
                $mail_pwd = 'TRUE';
            }

            $mail_pwd = 'TRUE'; //by default true
            if (isset($request['notify_admin'])) {
                $notify_admin = 1;
            }
            if (isset($request['newsletter'])) {
                $newsletter = 1;
            }
            if (isset($request['unusual'])) {
                $unusual = 1;
            }


            if ($user) {
                $userMeta = UserMeta::where('userId', $user->id)->first();
                if ($userMeta == null) {
                    $userMeta = new UserMeta();
                    $userMeta->userId = $user->id;
                }

                $userMeta->notify_admin = $notify_admin;
                $userMeta->newsletter = $newsletter;
                $userMeta->unusual = $unusual;

                $userMeta->pwd_chng = $mail_pwd;
                $userMeta->save_activity = $save_activity;

                $userMeta->save();
                $ret['msg'] = 'success';
                $ret['message'] = __('messages.update.success', ['what' => 'Account Settings']);
            } else {
                $ret['msg'] = 'warning';
                $ret['message'] = __('messages.update.warning');
            }
        }
        if ($type == 'pwd_change') {
            //validate data
            $validator = Validator::make($request->all(), [
                'old-password' => 'required|min:6',
                'new-password' => 'required|min:6',
                're-password' => 'required|min:6|same:new-password',
            ]);
            if ($validator->fails()) {
                $msg = __('messages.form.wrong');
                if ($validator->errors()->hasAny(['old-password', 'new-password', 're-password'])) {
                    $msg = $validator->errors()->first();
                }

                $ret['msg'] = 'warning';
                $ret['message'] = $msg;
                return response()->json($ret);
            } else {
                $user = Auth::user();
                if ($user) {
                    if (! Hash::check($request->input('old-password'), $user->password)) {
                        $ret['msg'] = 'warning';
                        $ret['message'] = __('messages.password.old_err');
                    } else {
                        $userMeta = UserMeta::where('userId', $user->id)->first();
                        $userMeta->pwd_temp = Hash::make($request->input('new-password'));
                        $cd = Carbon::now();
                        $userMeta->email_expire = $cd->copy()->addMinutes(60);
                        $userMeta->email_token = str_random(65);
                        if ($userMeta->save()) {
                            try {
                                $user->notify(new PasswordChange($user, $userMeta));
                                $ret['msg'] = 'success';
                                $ret['message'] = __('messages.password.changed');
                            } catch (\Exception $e) {
                                $ret['msg'] = 'warning';
                                $ret['message'] = __('messages.email.password_change',['email' => get_setting('site_email')]);
                            }
                        } else {
                            $ret['msg'] = 'warning';
                            $ret['message'] = __('messages.form.wrong');
                        }
                    }
                } else {
                    $ret['msg'] = 'warning';
                    $ret['message'] = __('messages.form.wrong');
                }
            }
        }
        if($type == 'google2fa_setup'){
            $google2fa = $request->input('google2fa', 0);
            $user = User::FindOrFail(Auth::id());
            if($user){
                // Google 2FA
                $ret['link'] = route('user.account');
                if(!empty($request->google2fa_code)){
                    $g2fa = new Google2FA();
                    if($google2fa == 1){
                        $verify = $g2fa->verifyKey($request->google2fa_secret, $request->google2fa_code);
                    }else{
                        $verify = $g2fa->verify($request->google2fa_code, $user->google2fa_secret);
                    }

                    if($verify){
                        $user->google2fa = $google2fa;
                        $user->google2fa_secret = ($google2fa == 1 ? $request->google2fa_secret : null);
                        $user->save();
                        $ret['msg'] = 'success';
                        $ret['message'] = __('Successfully '.($google2fa == 1 ? 'enable' : 'disable').' 2FA security in your account.');
                    }else{
                        $ret['msg'] = 'error';
                        $ret['message'] = __('You have provide a invalid 2FA authentication code!');
                    }
                }else{
                    $ret['msg'] = 'warning';
                    $ret['message'] = __('Please enter a valid authentication code!');
                }
            }
        }

        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }


    public function account_selfcert(Request $request)
    {
        $ret['msg'] = 'info';
        $ret['message'] = __('messages.nothing');
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
        $user = SelfCert::create([
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

        if($user){
            $user1 = User::FindOrFail(Auth::id());
            $user1->self_cert_form = 'complete';
            $user1->save();
            $ret['msg'] = 'Self Certification Success!';
        }


        if ($request->ajax()) {
            return response()->json($ret);
        }
        return redirect()->route('user.account')->with([$ret['msg'] => $ret['message']]);
    }

    public function password_confirm($token)
    {
        $user = Auth::user();
        $userMeta = UserMeta::where('userId', $user->id)->first();
        if ($token == $userMeta->email_token) {
            if (_date($userMeta->email_expire, 'Y-m-d H:i:s') >= date('Y-m-d H:i:s')) {
                $user->password = $userMeta->pwd_temp;
                $user->save();
                $userMeta->pwd_temp = null;
                $userMeta->email_token = null;
                $userMeta->email_expire = null;
                $userMeta->save();

                $ret['msg'] = 'success';
                $ret['message'] = __('messages.password.success');
            } else {
                $ret['msg'] = 'warning';
                $ret['message'] = __('messages.password.failed');
            }
        } else {
            $ret['msg'] = 'warning';
            $ret['message'] = __('messages.password.token');
        }

        return redirect()->route('user.account')->with([$ret['msg'] => $ret['message']]);
    }

    /**
     * Get pay now form
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function get_wallet_form(Request $request)
    {
        return view('modals.user_wallet')->render();
    }

    /**
     * Show the user Referral page
     *
     * @version 1.0.0
     * @since 1.0.3
     * @return void
     */
    public function referral()
    {
        $page = Page::where('slug', 'referral')->where('status', 'active')->first();
        // $reffered = User::where('referral', auth()->id())->get();
        $reffered =  User::leftJoin('referrals', function($join) {
         $join->on('users.id', '=', 'referrals.refer_by');
        })
        ->where('referrals.refer_by',auth()->id())
        ->get();
          
        if(get_page('referral', 'status') == 'active'){
            return view('user.referral', compact('page', 'reffered'));
        }else{
            abort(404);
        }
    }
}
