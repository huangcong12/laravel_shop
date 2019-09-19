<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cartltem extends Model
{
    protected $fillable = ['amount'];

    /**
     * 一条购物车记录一个用户
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 一条购物车记录一个 sku
     *
     * @return BelongsToMany
     */
    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }
}
