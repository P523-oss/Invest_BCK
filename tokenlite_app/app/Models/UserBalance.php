<?php /** @noinspection ALL */

/**
 * User Model
 *
 * The User Model
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 */
namespace App\Models;

use Carbon\Carbon;
use App\Models\KYC;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property mixed walletAddress
 */
class UserBalance extends Authenticatable // implements MustVerifyEmail
{
    use Notifiable;

    /*
     * Table Name Specified
     */
    protected $table = 'user_balances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'token_id', 'contributed', 'tokenBalance',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    //     'password', 'remember_token',
    // ];

   

     /**
     *
     * Relation with Activity logs
     *
     * @version 1.0.0
     * @since 1.0
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function logs()
    {
        return $this->belongsTo('App\Models\Activity', 'id', 'user_id');
    }

    /**
     *
     * Relation with UserBalance
     *
     * @version 1.0.0
     * @since 1.0
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function token()
    {
        return $this->belongsTo('App\Models\Token', 'id', 'token_id');
    }

     /**
     *
     * Check user role
     *
     * @version 1.0.0
     * @since 1.1.5
     * @return boolean
     */
    public function is($name)
    {
        return $this->role == $name;
    }

}
