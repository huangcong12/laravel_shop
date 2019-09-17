<?php

// @formatter:off

/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Support\Carbon;

    /**
     * App\UserAddress
     *
     * @property int $id
     * @property int $user_id
     * @property string $province
     * @property string $city
     * @property string $district
     * @property string $address
     * @property int $zip
     * @property string $contact_name
     * @property string $contact_phone
     * @property Carbon|null $last_used_at
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read mixed $full_address
     * @property-read User $user
     * @method static Builder|UserAddress newModelQuery()
     * @method static Builder|UserAddress newQuery()
     * @method static Builder|UserAddress query()
     * @method static Builder|UserAddress whereAddress($value)
     * @method static Builder|UserAddress whereCity($value)
     * @method static Builder|UserAddress whereContactName($value)
     * @method static Builder|UserAddress whereContactPhone($value)
     * @method static Builder|UserAddress whereCreatedAt($value)
     * @method static Builder|UserAddress whereDistrict($value)
     * @method static Builder|UserAddress whereId($value)
     * @method static Builder|UserAddress whereLastUsedAt($value)
     * @method static Builder|UserAddress whereProvince($value)
     * @method static Builder|UserAddress whereUpdatedAt($value)
     * @method static Builder|UserAddress whereUserId($value)
     * @method static Builder|UserAddress whereZip($value)
     */
    class UserAddress extends Eloquent
    {
    }
}

namespace App {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Support\Carbon;

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
     */
    class Product extends Eloquent
    {
    }
}

namespace App {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Notifications\DatabaseNotification;
    use Illuminate\Notifications\DatabaseNotificationCollection;
    use Illuminate\Support\Carbon;

    /**
     * App\User
     *
     * @property int $id
     * @property string $name
     * @property string $email
     * @property Carbon|null $email_verified_at
     * @property string $password
     * @property string|null $remember_token
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read Collection|UserAddress[] $address
     * @property-read int|null $address_count
     * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
     * @property-read int|null $notifications_count
     * @method static Builder|User newModelQuery()
     * @method static Builder|User newQuery()
     * @method static Builder|User query()
     * @method static Builder|User whereCreatedAt($value)
     * @method static Builder|User whereEmail($value)
     * @method static Builder|User whereEmailVerifiedAt($value)
     * @method static Builder|User whereId($value)
     * @method static Builder|User whereName($value)
     * @method static Builder|User wherePassword($value)
     * @method static Builder|User whereRememberToken($value)
     * @method static Builder|User whereUpdatedAt($value)
     */
    class User extends Eloquent
    {
    }
}

namespace App {

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Support\Carbon;

    /**
     * App\ProductSku
     *
     * @property int $id
     * @property string $title
     * @property string $description
     * @property float $price
     * @property int $stock
     * @property int $product_id
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read Product $product
     * @method static Builder|ProductSku newModelQuery()
     * @method static Builder|ProductSku newQuery()
     * @method static Builder|ProductSku query()
     * @method static Builder|ProductSku whereCreatedAt($value)
     * @method static Builder|ProductSku whereDescription($value)
     * @method static Builder|ProductSku whereId($value)
     * @method static Builder|ProductSku wherePrice($value)
     * @method static Builder|ProductSku whereProductId($value)
     * @method static Builder|ProductSku whereStock($value)
     * @method static Builder|ProductSku whereTitle($value)
     * @method static Builder|ProductSku whereUpdatedAt($value)
     */
    class ProductSku extends Eloquent
    {
    }
}

