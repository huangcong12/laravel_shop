<?php

namespace App;

use App\Exceptions\InternalException;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;


class ProductSku extends Model
{
    protected $fillable = [
        'title', 'description', 'price', 'stock'
    ];

    /**
     * 每个 sku 都有一个商品和它对应
     *
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 减库存
     *
     * @param $amount
     * @return int
     * @throws InternalException
     */
    public function decreaseStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('减库存不可小于 0');
        }

        return $this->where('id', $this->id)
            ->where('stock', '>=', $amount)
            ->decrement('stock', $amount);
    }

    /**
     * 加库存
     *
     * @param $amount
     * @throws InternalException
     */
    public function addStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('加库存不可小于 0');
        }
        $this->increment('stock', $amount);
    }

}
