<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tracking_code',
        'simcard_id',
        'buyer_name',
        'buyer_national_code',
        'buyer_phone',
        'buyer_father_name',
        'buyer_birthdate',
        'amount',
        'payment_status',
        'authority',
        'ref_id',
        'payment_message',
        'payment_verified_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public const STATUS_PENDING           = 'pending';
    public const STATUS_GATEWAY_REQUESTED = 'gateway_requested';
    public const STATUS_CANCELED          = 'canceled';
    public const STATUS_VERIFY_PENDING    = 'verify_pending';
    public const STATUS_SUCCESS           = 'success';
    public const STATUS_VERIFY_FAILED     = 'verify_failed';
    public const STATUS_FAILED            = 'failed';

    public const FINAL_STATUSES = [
        self::STATUS_SUCCESS,
        self::STATUS_VERIFY_FAILED,
        self::STATUS_FAILED,
    ];

    public static function paymentStatuses(): array
    {
        return [
            self::STATUS_PENDING           => 'در انتظار شروع پرداخت',
            self::STATUS_GATEWAY_REQUESTED => 'ارسال شده به درگاه',
            self::STATUS_CANCELED          => 'لغو شده / پرداخت تکمیل نشده',
            self::STATUS_VERIFY_PENDING    => 'در انتظار بررسی مجدد پرداخت',
            self::STATUS_SUCCESS           => 'موفق',
            self::STATUS_VERIFY_FAILED     => 'ناموفق در تأیید پرداخت',
            self::STATUS_FAILED            => 'ناموفق قطعی',
        ];
    }

    public static function paymentStatusLabel(?string $status): string
    {
        return self::paymentStatuses()[$status] ?? 'نامشخص';
    }
}
