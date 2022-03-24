<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaySystem extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public const PAY_SYSTEMS = [
        'tinkoff',
    ];
    public const CONFIG_SYSTEMS = [
        'tinkoff' => [
            'url'           =>  '',
            'terminalKey'  =>  '',
            'secretKey'     =>  '',
        ],
    ];

    public const FIELD_ID		= 'id';
    public const FIELD_CONFIG   = 'config';
    public const FIELD_LABEL    = 'label';
    public const FIELD_ACTIVE   = 'active';
    public const FIELD_SHOP_ID  = 'shop_id';

    protected $fillable = [
        self::FIELD_CONFIG,
        self::FIELD_LABEL,
        self::FIELD_ACTIVE,
        self::FIELD_SHOP_ID,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        self::FIELD_ID      => 'string',
        self::FIELD_CONFIG  => 'array',
    ];

    public function shops()
    {
        return $this->belongsTo(Shop::class,Shop::FIELD_ID,self::FIELD_SHOP_ID);
    }
}
