<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SelfCert extends Model
{

    /*
     * Table Name Specified
     */
    protected $table = 'selfcert';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'accredited', 'qualified1', 'qualified2', 'qualified3', 'qualified4','qualified5','annual_income','net_worth','us_citizen','backup'
    ];
}
