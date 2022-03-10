<?php
/**
 * AdminMiddleware
 *
 * Check the user is admin or not?
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 */
namespace App\Http\Middleware;

use Auth;
use Closure;
use App\Models\GlobalMeta;
use App\PayModule\Module;

class SelfCertifiedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if (!$user->selfCertified()) {
            return redirect()->route('user.selfcertify');
            //->withErrors(['msg', 'You have not completed Self Certification process.']);
        }
        return $next($request);
    }
}
