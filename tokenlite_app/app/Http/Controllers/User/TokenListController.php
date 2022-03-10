<?php

namespace App\Http\Controllers\User;
/**
 * Token Controller
 *
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0.0
 */
use Auth;
use App\Models\IcoStage;
use App\PayModule\Module;
use App\Models\Token;
use Illuminate\Http\Request;
use App\Notifications\TnxStatus;
use App\Http\Controllers\Controller;

class TokenListController extends Controller
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

        $tkns = Token::where([
            ['hidden','=',0],
            ['status','=','active']
        ])->get();
        return view('user.token-list', compact('tkns'));
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
}
