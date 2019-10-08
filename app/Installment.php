<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;

class Installment extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_REPAYING = 'repaying';
    const STATUS_FINISHED = 'finished';

    public static $statusMap = [
        self::STATUS_PENDING => '未执行',
        self::STATUS_REPAYING => '还款中',
        self::STATUS_FINISHED => '已还款',
    ];

    protected $fillable = [
        'no',
        'total_amount',
        'count',
        'fee_rate',
        'fine_rate',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            if (!$model->no) {
                // 调用 findAvailableNo 生成分期流水号
                $model->no = static::findAvailableNo();
                // 如果生成失败，停止创建订单
                if (!$model->no) {
                    return false;
                }
            }
        });
    }

    /**
     * 生成分期流水号
     */
    public static function findAvailableNo()
    {
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            $no = $prefix . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否存在
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
        }
        Log::warning(sprintf('find installment no failed'));

        return false;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(InstallmentItem::class);
    }
}
