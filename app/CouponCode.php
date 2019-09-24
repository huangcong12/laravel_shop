<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CouponCode extends Model
{
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED => '固定金额',
        self::TYPE_PERCENT => '比例',
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean'
    ];

    protected $dates = ['not_before', 'not_after'];

    /**
     * 生成优惠券编码
     *
     * @param int $length
     */
    public static function findAvailableCode($length = 6)
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }

    public function getDescriptionAttribute($minAmount = '', $type = '', $value = '')
    {
        $minAmount = $minAmount ?: $this->min_amount;
        $type = $type ?: $this->type;
        $value = $value ?: $this->value;

        $str = '';
        if ($minAmount > 0) {
            $str = '满' . $minAmount;
        }
        if ($type === self::TYPE_PERCENT) {
            return $str . '优惠' . $value . '%';
        }

        return $str . '减' . $value;
    }
}
