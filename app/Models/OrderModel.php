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
        'secure_token',
        'admin_status',
        'address',
        'postal_code',
        'national_id_document_path',
        'selfie_document_path',
        'documents_uploaded_at',
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

    public const ADMIN_STATUS_DOCUMENTS_PENDING = 'documents_pending';
    public const ADMIN_STATUS_DOCUMENTS_UPLOADED = 'documents_uploaded';
    public const ADMIN_STATUS_UNDER_REVIEW = 'under_review';
    public const ADMIN_STATUS_APPROVED = 'approved';
    public const ADMIN_STATUS_REJECTED = 'rejected';
    public const ADMIN_STATUS_COMPLETED = 'completed';
    public const ADMIN_STATUS_CANCELLED = 'cancelled';

    public static function adminStatuses(): array
    {
        return [
            self::ADMIN_STATUS_DOCUMENTS_PENDING => 'در انتظار تکمیل مدارک',
            self::ADMIN_STATUS_DOCUMENTS_UPLOADED => 'مدارک بارگذاری شد',
            self::ADMIN_STATUS_UNDER_REVIEW => 'در حال بررسی',
            self::ADMIN_STATUS_APPROVED => 'تأیید شده',
            self::ADMIN_STATUS_REJECTED => 'رد شده',
            self::ADMIN_STATUS_COMPLETED => 'تکمیل شده',
            self::ADMIN_STATUS_CANCELLED => 'لغو شده',
        ];
    }

    public static function paymentStatusLabel(?string $status): string
    {
        return self::paymentStatuses()[$status] ?? 'نامشخص';
    }
}
