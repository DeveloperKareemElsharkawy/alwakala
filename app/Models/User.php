<?php

namespace App\Models;

use App\Enums\UserTypes\UserType;
use App\Lib\Services\ImageUploader\UploadImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $hidden = [
        'password',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'email',
        'password',
        'name',
        'image',
        'mobile',
        'activation',
        'share_coupon_code',
        'type_id'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if(isset($this->image)){
            return config('filesystems.aws_base_url') . $this->image;
        }else{
            return \URL::asset('/admin/assets/images/users/48/girl.png');
        }
    }

    public function AauthAcessToken()
    {
        return $this->hasMany(OauthAccessToken::class);
    }

    public function initializeUserFields($data)
    {
        $this->name = $data['name'];
        if(isset($data['password'])){
            $this->password = bcrypt($data['password']);
        }
        if (isset($data['email']))
            $this->email = $data['email'];
        $this->mobile = $data['mobile'];
        $this->type_id = $data['type_id'];

        if ($data['type_id'] == UserType::SELLER && array_key_exists('image', $data)) {
            $this->image = UploadImage::uploadImageToStorage($data['image'], 'sellers');

        }
        if ($data['type_id'] == UserType::ADMIN) {
            if (isset($data['image'])) {
                $this->image = UploadImage::uploadImageToStorage($data['image'], 'admins');
            }
            $this->activation = $data['activation'];
        } elseif ($data['type_id'] == UserType::CONSUMER) {
            if (isset($data['image'])) {
                $this->image = UploadImage::uploadImageToStorage($data['image'], 'consumer');
            }
        } else {
            $this->activation = false;
//            $this->image = UploadImage::uploadImageToStorage($data['image'], 'sellers');
        }
        $this->activation = $data['activation'] ? $data['activation'] : false;
    }

    public function seller()
    {
        return $this->hasOne(Seller::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class)->with('role');
    }

    public function favorites()
    {
        return $this->morphMany(SellerFavorite::class, 'favoriter');
    }

    public function stores()
    {
        return $this->hasOne(Store::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class , 'owner_id');
    }

    public function device_token()
    {
        return $this->hasOne(UserDeviceToken::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'sellers');
    }

    public function warehourses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->whereHas('items', function ($q) {
            $q->whereHas('store');
        });
    }

}
