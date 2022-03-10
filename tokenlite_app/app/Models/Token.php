<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $token_symbol
 * @property string $short_description
 * @property string $description
 * @property string $url_more_info
 * @property string $logo
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $require_sign
 * @property string $sign_info
 */
class Token extends Model
{
    /**
     * @var array
     */

    protected $table = 'tokens';

    protected $fillable = ['name', 'token_symbol', 'short_description', 'description', 'url_more_info', 'logo', 'status', 'created_at', 'updated_at', 'require_sign', 'sign_info'];

    public static function getSymbol($token_id)
    {
        $sym = self::where('id', $token_id)->first()->token_symbol;
        return $sym;
    }

    public static function requireSign($require_sign)
    {
        return (notNullValue($require_sign) && $require_sign == 1);
    }

    public static function getBySymbol($symbol)
    {
        $tkn = self::where('token_symbol', $symbol)->first();
        return $tkn;
    }

    public function createStages()
    {
        // protected $fillable = ['token_id', 'name', 'start_date', 'end_date', 'total_tokens', 'base_price', 'display_mode'];
        $current_date = date('Y-m-d H:i:s');
        $stg_id =0;
        for ($i=1; $i<=6; $i++){
            $stg = IcoStage::create([
                'token_id' => $this->id,
                'name' => "Token Stage ". $i,
                'start_date' => now()->addMonth($i-1),
                'end_date' => now()->addMonth($i),
                'total_tokens' => 850000,
                'base_price' => 0.2,
                'display_mode' => "normal"
            ]);
            $stg->min_purchase = '100';
            $stg->max_purchase = '10000';
            $stg->save();
            // takes first stage id created
            if ($stg_id ==0)
                $stg_id = $stg->id;
            IcoMeta::create([
                'token_id' => $stg->token_id,
                'stage_id' => $stg->id,
                'option_name' => 'bonus_option',
                'option_value' => self::default_ico_meta('bonus_option', 'json'),
            ]);
            IcoMeta::create([
                'token_id' => $stg->token_id,
                'stage_id' => $stg->id,
                'option_name' => 'price_option',
                'option_value' => self::default_ico_meta('price_option', 'json'),
            ]);
        }
        return $stg_id;
    }

    public static function default_ico_meta($which, $type = 'object')
    {
        $end = now()->addDays(25)->format('Y-m-d H:i:s');

        $prices = [
            'tire_1' => [
                'price' => 0,
                'min_purchase' => 0,
                'start_date' => def_datetime('datetime'),
                'end_date' => def_datetime('datetime_e'),
                'status' => 0,
            ],
            'tire_2' => [
                'price' => 0,
                'min_purchase' => 0,
                'start_date' => def_datetime('datetime'),
                'end_date' => def_datetime('datetime_e'),
                'status' => 0,
            ],
            'tire_3' => [
                'price' => 0,
                'min_purchase' => 0,
                'start_date' => def_datetime('datetime'),
                'end_date' => def_datetime('datetime_e'),
                'status' => 0,
            ],

        ];

        $bonuses = [
            'base' => [
                'amount' => 25,
                'start_date' => def_datetime('datetime'),
                'end_date' => def_datetime('datetime_e'),
                'status' => 1,
            ],
            'bonus_amount' => [
                'status' => 1,
                'tire_1' => [
                    'amount' => 15,
                    'token' => 2500,
                ],
                'tire_2' => [
                    'amount' => null,
                    'token' => null,
                ],
                'tire_3' => [
                    'amount' => null,
                    'token' => null,
                ],
            ],
        ];

        if ($which == 'price_option') {
            $result = json_encode($prices);
        }
        if ($which == 'bonus_option') {
            $result = json_encode($bonuses);
        }
        if ($type == 'json') {
            return $result;
        }
        return json_decode($result);
    }

    public static function get_actived_stage($tkn_id)
    {
        $tkn = self::where('id', $tkn_id)->first();
        $actived_stage = $tkn->actived_stage;
        if (is_null($actived_stage)){
        }
        return $tkn->actived_stage;
    }
}
