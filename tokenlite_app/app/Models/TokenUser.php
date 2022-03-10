<?php
/**
 * User Model
 *
 * Store the users meta data
 *
 * @package TokenLite
 * @author Softnio
 * @version 1.0
 */
namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class TokenUser extends Model
{

    /*
     * Table Name Specified
     */
    protected $table = 'token_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'tokenId', 'userId', 'signed', 'signed_info'];

    public static function IsSigned($tokenId, $userId)
    {
        $tu= self::where('tokenId', $tokenId)->where('userId', $userId)->first();
        if($tu)
            return ($tu->signed==1);
        else
            return false;
    }

     /**
     *
     * Relation with token
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function token()
    {
        return $this->belongsTo('App\Models\Token', 'tokenId', 'id');
    }

         /**
     *
     * Relation with user
     *
     * @version 1.0.0
     * @since 1.0
     * @return void
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'userId', 'id');
    }
}
