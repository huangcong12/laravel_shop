<?php

namespace App;

use Eloquent;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * App\Product
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property bool $on_sale
 * @property float $rating
 * @property int $sold_count
 * @property int $review_count
 * @property float $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $image_url
 * @property-read Collection|ProductSku[] $skus
 * @property-read int|null $skus_count
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDescription($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereImage($value)
 * @method static Builder|Product whereOnSale($value)
 * @method static Builder|Product wherePrice($value)
 * @method static Builder|Product whereRating($value)
 * @method static Builder|Product whereReviewCount($value)
 * @method static Builder|Product whereSoldCount($value)
 * @method static Builder|Product whereTitle($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @mixin Eloquent
 */
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

    /**
     * 获取完整的图片链接
     */
    public function getImageUrlAttribute()
    {
        if (filter_var($this->attributes['image'], FILTER_VALIDATE_URL)) {
            return $this->attributes['image'];
        }

        return Storage::disk('public')->url($this->attributes['image']);
    }
}
