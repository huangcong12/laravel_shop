<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
 * @mixin Eloquent
 */
class UserAddress extends Model
{
    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at',
    ];

    protected $dates = ['last_used_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
