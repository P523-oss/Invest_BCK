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

class ListingController extends Controller
{
    /**
     * Display a listing .
     *
     * @return \Illuminate\Http\Response
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function index()
    {
        // return view('user.listing'); 
        return redirect('/user');
    }

}
