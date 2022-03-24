<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\PaySystem;

class Shop extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public const TAX = [
        'osn',
        'usn_income',
        'usn_outcome',
        'patent',
        'envd',
        'esn',
    ];

    public const FIELD_ID		     = 'id';
    public const FIELD_USER_ID		 = 'user_id';
    public const FIELD_LABEL		 = 'label';
    public const FIELD_ACTIVE		 = 'active';
    public const FIELD_URL		     = 'url';
    public const FIELD_SUCCESS_URL	 = 'success_url';
    public const FIELD_FAIL_URL		 = 'fail_url';
    public const FIELD_EMAIL		 = 'email';
    public const FIELD_INN		     = 'inn';
    public const FIELD_ACCOUNTER	 = 'accounter';
    public const FIELD_ACCOUNTER_INN = 'accounter_inn';
    public const FIELD_TAX		     = 'tax';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        self::FIELD_USER_ID,
        self::FIELD_LABEL,
        self::FIELD_ACTIVE,
        self::FIELD_URL,
        self::FIELD_SUCCESS_URL,
        self::FIELD_FAIL_URL,
        self::FIELD_EMAIL,
        self::FIELD_INN,
        self::FIELD_ACCOUNTER,
        self::FIELD_ACCOUNTER_INN,
        self::FIELD_TAX,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        self::FIELD_ID  => 'string',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function users()
    {
        return $this->belongsTo(User::class,User::FIELD_ID,self::FIELD_USER_ID);
    }

    public function paySystems()
    {
        return $this->hasMany(PaySystem::class,PaySystem::FIELD_ID,self::FIELD_ID);
    }
}
