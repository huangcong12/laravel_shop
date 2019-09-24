<?php

namespace App;

use App\Exceptions\CouponCodeUnavailableException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
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

    protected $appends = ['description'];

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

    /**
     * 生成 description 属性
     * @return string
     */
    public function getDescriptionAttribute()
    {
        $str = '';
        if ($this->min_amount > 0) {
            $str = '满' . $this->min_amount;
        }
        if ($this->type === self::TYPE_PERCENT) {
            return $str . '优惠' . $this->value . '%';
        }

        return $str . '减' . $this->value;
    }

    /**
     * 校验优惠券是否可使用
     *
     * @param null $orderAmount
     * @throws CouponCodeUnavailableException
     */
    public function checkAvailable($orderAmount = null)
    {
        if (!$this->enabled) {
            throw new CouponCodeUnavailableException('优惠券不存在');
        }

        if ($this->total - $this->used <= 0) {
            throw new CouponCodeUnavailableException('优惠券已被兑换完');
        }

        if ($this->not_before && $this->not_before->gt(Carbon::now())) {
            throw new CouponCodeUnavailableException('优惠券还未能使用');
        }
        if ($this->not_after && $this->not_after->lt(Carbon::now())) {
            throw new CouponCodeUnavailableException('优惠券已过期');
        }

        if (!is_null($orderAmount) && $orderAmount <= $this->min_amount) {
            throw new CouponCodeUnavailableException('订单金额不满足优惠券使用最低限额');
        }
    }

    /**
     * 计算优惠价格
     *
     * @param $orderAmount
     * @return mixed|string
     */
    public function getAdjustedPrice($orderAmount)
    {
        if ($this->type === self::TYPE_FIXED) {
            return max(0.01, $orderAmount - $this->value);
        }

        return number_format($orderAmount * (100 - $this->value) / 100, 2, '.', '');
    }

    /**
     * 修改优惠券使用量
     *
     * @param bool $increase
     */
    public function changeUsed($increase = true)
    {
        if ($increase) {
            return $this->where('id', $this->id)->where('used', '<', $this->total)->increment('used');
        } else {
            return $this->decrement('used');
        }
    }
}
