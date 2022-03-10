<?php

namespace App\Http\Controllers\Admin;
/**
 * Token Controller
 *
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0.0
 */
use Auth;
use Validator;
use App\Models\IcoStage;
use App\PayModule\Module;
use App\Models\Token;
use App\Models\User;
use App\TokenSettings;
use Illuminate\Http\Request;
use App\Notifications\TnxStatus;
use App\Http\Controllers\Controller;

class TokenSetupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function index()
    {
        //Token::where(['status' =>"A"])->delete();
        $tkns = Token::all();
        $clients = User::where('role','clientadmin')->get();
        return view('admin.token-setup', compact('tkns','clients'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     *
     * @throws \Throwable
     */
    public function show(Request $request, $id='')
    {
        $module = new Module();
        $tid = ($id == '' ? $request->input('tkn_id') : $id);
        if ($tid != null) {
            $tkn = Token::find($tid);
            return $module->show_details($tkn);
        } else {
            return false;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     */
    public function destroy(Request $request, $id='')
    {
        $tid = ($id == '' ? $request->input('tkn_id') : $id);
        if ($tid != null) {
            $tnx = Token::FindOrFail($tid);
            if ($tnx) {
                $old = $tnx->status;
                $tnx->status = 'deleted';
                $tnx->save();
                if ($old == 'pending' || $old == 'onhold') {
                    IcoStage::token_add_to_account($tnx, 'sub');
                }
                $ret['msg'] = 'error';
                $ret['message'] = __('messages.delete.delete', ['what'=>'Token']);
            } else {
                $ret['msg'] = 'warning';
                $ret['message'] = 'This Token is not available now!';
            }
        } else {
            $ret['msg'] = 'warning';
            $ret['message'] = __('messages.delete.failed', ['what'=>'Token']);
        }

        if ($request->ajax()) {
            return response()->json($ret);
        }
        return back()->with([$ret['msg'] => $ret['message']]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @version 1.0.2
     * @since 1.0
     * @return void
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'short_description' => 'required|min:3',
            'description' => 'required|min:3',
            'token_symbol' => 'required',
        ]);

        if ($validator->fails()) {
            $msg = '';
            if ($validator->errors()->hasAny(['name', 'short_description', 'description', 'token_symbol','client_id','color'])) {
                $msg = $validator->errors()->first();
            } else {
                $msg = __('messages.somthing_wrong');
            }

            $ret['msg'] = 'warning';
            $ret['message'] = $msg;
            return response()->json($ret);
        } else {
            if(($request->input('role')=='admin' || $request->input('role')=='supervisor') && !super_access()) {
                $ret['msg'] = 'warning';
                $ret['message'] = __("You do not have enough permissions to perform requested operation.");
            } else {
                $nameExists = (Token::where('name', '=', $request->input('name'))->count() > 0);
                $symbolExists = (Token::where('token_symbol', '=', $request->input('token_symbol'))->count() > 0);
                if ($nameExists || $symbolExists){
                    $ret['msg'] = 'warning';
                    $ret['message'] = __("There is already a record with this "). ($nameExists? "Name": "Symbol");
                } else {
                    $filename = $this->generateFileName($request); 
                    $statusActive = $request->has('status');
                    $token = Token::create([
                        'name' => $request->input('name'),
                        'token_symbol' => $request->input('token_symbol'),
                        'short_description' => $request->input('short_description'),
                        'description' => $request->input('description'),
                        'token_symbol' => $request->input('token_symbol'),
                        'url_more_info' => $request->input('url_more_info'),
                        // 'logo' => $request->input('logo'),
                        'logo' => $filename,
                        'status' => $statusActive ? "active" : "suspend",
                    ]);
                    
                    if ($token) {
                        // Create stages and set first stage as Active Stage in Token
                        $active_stage = $token->createStages();
                        $token->actived_stage = $active_stage;
                        $token->save();

                        $token_settings = TokenSettings::create([
                            'token_id' => $token->id,
                            'client_id' => (int)$request->input('client_id'),
                            'token_logo' => $filename,
                            'bg_color' => $request->input('color')
                        ]);

                        $token_settings->save();

                        $ret['link'] = route('admin.tokens.settings');
                        $ret['msg'] = 'success';
                        $ret['message'] = __('messages.insert.success', ['what' => 'Token']);
                    } else {
                        $ret['msg'] = 'warning';
                        $ret['message'] = __('messages.insert.warning', ['what' => 'Token']);
                    }
                }
            }
              
            if ($request->ajax()) {
                return response()->json($ret);
            }
            return back()->with([$ret['msg'] => $ret['message']]);
        }
    }

    public function status(Request $request)
    {

        $id = $request->input('uid');
        $type = $request->input('req_type');

        if(!super_access()) {
            $up = Token::where('id', $id)->first();
            if($up) {
                if($up->role!='token') {
                    $result['msg'] = 'warning';
                    $result['message'] = __("You do not have enough permissions to perform requested operation.");
                    return response()->json($result);
                }
            }
        }

        if ($type == 'suspend_token') {
            $up = Token::where('id', $id)->update([
                'status' => 'suspend',
            ]);
            if ($up) {
                $result['msg'] = 'warning';
                $result['css'] = 'danger';
                $result['status'] = 'active_user';
                $result['message'] = 'Token Suspend Success!!';
            } else {
                $result['msg'] = 'warning';
                $result['message'] = 'Failed to Suspend!!';
            }

            return response()->json($result);
        }
        if ($type == 'active_token') {
            $up = Token::where('id', $id)->update([
                'status' => 'active',
            ]);
            if ($up) {
                $result['msg'] = 'success';
                $result['css'] = 'success';
                $result['status'] = 'suspend_user';
                $result['message'] = 'Token Active Success!!';
            } else {
                $result['msg'] = 'warning';
                $result['message'] = 'Failed to Active!!';
            }
            return response()->json($result);
        }
    }


    /**
     * Upload the files
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function upload_zone(Request $request)
    {
        //passport upload
        if ($request->hasFile('whitepaper')) {
            $cleanData = Validator::make($request->all(), ['whitepaper' => 'required|mimetypes:application/pdf']);
            $old = storage_path('app/public/' . get_setting('site_white_paper'));
            if ($cleanData->fails()) {
                $ret['msg'] = 'warning';
                $ret['message'] = __('messages.upload.invalid');
            } else {
                $file = $request->file('whitepaper');
                $name = 'white-paper' . time() . '.' . $file->extension();
                $file->move(storage_path('app/public/'), $name);
                Setting::updateValue('site_white_paper', $name);

                $ret['msg'] = 'success';
                $ret['message'] = __('messages.upload.success', ['what' => "White Paper"]);
                $ret['file_name'] = $name;
                if (! is_dir($old) && ! starts_with($old, 'assets')) {
                    unlink($old);
                }
            }
            return response()->json($ret);
        }
    }

    public function updateToken(Request $request){
        /*
            **hide or shows token
        */
        $up = Token::where('id', $request->id)->update([
            'hidden' => $request->hidden,
        ]);
        if ($up) {
            $result['msg'] = 'success';
            $result['css'] = 'success';
            $result['code'] = 201;
            $result['message'] = 'Token updated successfully';
        } else {
            $result['msg'] = 'warning';
            $result['code'] = 404;
            $result['message'] = 'Something went wrong please try again later.';
        }

        return response()->json($result);
    }

    public function editToken(Request $request,$id=""){
        /*
            **edit token details
        */
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'short_description' => 'required|min:3',
            'description' => 'required|min:3',
            'token_symbol' => 'required'
        ]);
        
        if ($validator->fails()) {
            $msg = '';
            if ($validator->errors()->hasAny(['name', 'short_description', 'description', 'token_symbol'])) {
                $msg = $validator->errors()->first();
            } else { $msg = __('messages.somthing_wrong');}

            $ret['msg'] = 'warning';
            $ret['message'] = $msg;
            return response()->json($ret);
        } else {
            if(($request->input('role')=='admin' || $request->input('role')=='supervisor') && !super_access()) {
                $ret['msg'] = 'warning';
                $ret['message'] = __("You do not have enough permissions to perform requested operation.");
            } else {
                $nameExists = (Token::where([['name', '=', $request->input('name')],['id', '!=', $id]])->count() > 0);
                $symbolExists = (Token::where([['token_symbol', '=', $request->input('token_symbol')],['id', '!=', $id]])->count() > 0);
                if ($nameExists || $symbolExists){
                    $ret['msg'] = 'warning';
                    $ret['message'] = __("There is already a record with this "). ($nameExists? "Name": "Symbol");
                } else {
                    /*
                         Continue to update tokens
                    */
                    $statusActive = $request->has('status');
                    $data = [
                        'name' => $request->input('name'),
                        'token_symbol' => $request->input('token_symbol'),
                        'short_description' => $request->input('short_description'),
                        'description' => $request->input('description'),
                        'token_symbol' => $request->input('token_symbol'),
                        'url_more_info' => $request->input('url_more_info'),
                        'status' => $statusActive ? "active" : "suspend",
                    ];

                    if($request->input('logo') != null){
                       $filename = $this->generateFileName($request);
                       $data['logo'] = $filename;
                    }
                    $token = Token::where('id',$id)->update($data);
                    if ($token) {
                        // Update token
                        $ret['link'] = route('admin.tokens.settings');
                        $ret['msg'] = 'success';
                        $ret['message'] = 'Token updated Successfully';
                    } else {
                        $ret['msg'] = 'warning';
                        $ret['message'] = 'Failed to update token. Please try again later';
                    }
                }
            }
              
            if ($request->ajax()) {
                return response()->json($ret);
            }
            return back()->with([$ret['msg'] => $ret['message']]);
        }
    }

    public function generateFileName($request){
        $exploded = explode(',',$request->logo_base);
        $decoded = base64_decode($exploded[1]);
        if(str_contains($exploded[0],'jpeg')){   $extension = 'jpg';}
        else{ $extension = 'png';}
        //generate random strings
        $length= 10;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) { $randomString .= $characters[rand(0, $charactersLength - 1)];} 
        $filename = $randomString.'.'.$extension;
        $public_path = str_replace(array('/tokenlite_app/public','\\tokenlite_app\\public'),array('/public_html','\\public_html'),public_path());
        // $public_path = public_path();
       
        $path = $public_path.'/images/symbol/'.$filename;
        $putfile_to_server=file_put_contents($path,$decoded);

        $logo_path = $public_path.'/images/logo/'.$filename;
        $putfile_to_server=file_put_contents($logo_path,$decoded);

        return $filename;
    }
}
