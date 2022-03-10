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

class Country extends Model
{

    /*
     * Table Name Specified
     */
    protected $table = 'countries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'code', 'name', 'is_active'];

}
