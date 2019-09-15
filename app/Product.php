<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'title', 'description', 'image', 'on_sale',
        'rating', 'sold_count', 'review_count', 'price'
    ];

    protected $casts = [
        'on_sale' => 'boolean',
    ];

    /**
     * 一个商品有多少 sku
     *
     * @return HasMany
     */
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }
}
