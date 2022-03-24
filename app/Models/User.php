<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Shop;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const TABLE              = 'users';
    public const FIELD_ID           = 'id';
    public const FIELD_NAME         = 'name';
    public const FIELD_EMAIL        = 'email';
    public const FIELD_PASSWORD     = 'password';
    public const FIELD_CREATED_AT   = 'created_at';
    public const FIELD_UPDATED_AT   = 'updated_at';
    public const FIELD_DELETED_AT   = 'deleted_at';
    public const FIELD_REMEMBER_TOKEN = 'remember_token';
    public const FIELD_ACTIVE       = 'active';


    public $incrementing = false;
    protected $primaryKey = self::FIELD_ID;
    protected $keyType = 'string';



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        self::FIELD_NAME,
        self::FIELD_EMAIL,
        self::FIELD_PASSWORD,
        self::FIELD_ACTIVE,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        self::FIELD_PASSWORD,
        self::FIELD_REMEMBER_TOKEN,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        self::FIELD_ID             => 'string',
        self::FIELD_CREATED_AT     => 'datetime',
        self::FIELD_UPDATED_AT    => 'datetime',
        self::FIELD_DELETED_AT    => 'datetime',
    ];

    public function shops()
    {
        return $this->hasMany(Shop::class,Shop::FIELD_USER_ID,self::FIELD_ID);
    }
}
