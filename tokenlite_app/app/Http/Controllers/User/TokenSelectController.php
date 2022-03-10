<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
Use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
Use App\Models\Token;

use Auth;
use App\Models\Transaction;

/**
 * Token Controller
 *
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0.0
 */


class TokenSelectController extends Controller
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

    }

     public function select(Request $request, $tokenSymbol="")
    {
        // token validation against DB
        // $tokenSymbol = strtoupper($request->input('tokensymbol'));
        $tokenSymbol = strtoupper($tokenSymbol);
        if (isset($tokenSymbol) && $tokenSymbol != '') {
            $currentToken = Token::getBySymbol($tokenSymbol);
            if(isset($currentToken)){
                Session::put('currenttoken', serialize($currentToken));
                Session::put('currentsymbol', $tokenSymbol);
            }
            else {
                Session::forget('currenttoken');
                Session::forget('currentsymbol');
            }
        }
        else {
                Session::forget('currenttoken');
                Session::forget('currentsymbol');
        }

        // return redirect()->route('home');
        if(!Auth::check()) return redirect()->route('home');
        
        $user = Auth::user();

        $stage = $active_bonus = $contribution = null;
        if (session()->has('currentsymbol')) {
            $stage = active_stage();
            $contribution = Transaction::user_contribution();
            $tc = new \App\Helpers\TokenCalculate();
            $active_bonus = $tc->get_current_bonus('active');
            $user->symbol = $tokenSymbol;
        }
        
        return view('user.dashboard', compact('user', 'stage', 'active_bonus', 'contribution'));
    }
}
