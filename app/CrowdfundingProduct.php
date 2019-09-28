<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrowdfundingProduct extends Model
{
    // 定义众筹的 3 种状态
    const STATUS_FUNDING = 'funding';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    public static $statusMap = [
        self::STATUS_FUNDING => '众筹中',
        self::STATUS_SUCCESS => '众筹成功',
        self::STATUS_FAIL => '众筹失败'
    ];
    public $timestamps = false;

    // end_at 会自动转为 Carbon 类型
    protected $fillable = [
        'total_amount',
        'target_amount',
        'user_count',
        'end_at',
        'status',
    ];

    // 不需要 created_at 和 updated_at 字段
    protected $dates = ['end_at'];

    /**
     * 每个众筹对应一个商品
     *
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 定义一个名为 percent 的访问器，返回当前众筹的进度
     */
    public function getPercentAttribute()
    {
        $value = $this->total_amount / $this->target_amount;

        return floatval(number_format($value * 100, 2, '.', ''));
    }
}
