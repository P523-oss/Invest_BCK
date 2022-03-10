<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TokenSettings extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token_id',
        'client_id',
        'token_logo',
        'bg_color',
    ];

    protected $hidden = ['updated_at','created_at'];
    protected $table = 'token_settings';
}
