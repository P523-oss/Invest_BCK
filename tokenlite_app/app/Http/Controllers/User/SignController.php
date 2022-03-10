<?php

namespace App\Http\Controllers\User;
/**
 * Kyc Controller
 *
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0.6
 */

use App\Exceptions\APIException;
use Auth;
use Illuminate\Http\Request;
use App\Helpers\DocuSignLoader;
use Exception;

class SignController extends BaseSignController
{
    protected $docuSignLoader;

    /**
     * Show the status
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function index()
    {
        $currentToken = session()->has('currenttoken')? unserialize(session('currenttoken')) : "";
        if (!$currentToken)
            return (null);
        $token_id =$currentToken->id;

        $signed_status = Auth::user()->tokens_users_signed_status($token_id);
        return view('user.signage', compact('signed_status'));
    }

    /**
     * Show the status
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function post(Request $request)
    {
        $clientParams['email'] = Auth::user()->email;
        $clientParams['name'] = Auth::user()->name;
        $clientParams['clientId'] = Auth::id();
        $this->docuSignLoader = new DocuSignLoader($clientParams);
        try {
            $signingView = $this->docuSignLoader->startSignage();
            $signed_status = 'submited';
            return view('user.signage', compact('signed_status'));
        } catch (Exception $ex){
            $msg_error = $ex->getMessage();
            $signed_status = 'submit-error';
            return view('user.signage', compact('signed_status', 'msg_error'));
        }
    }
}
