<?php

namespace App\Models;

use CodeIgniter\Model;

class InventorySettingModel extends Model
{
    protected $table            = 'inventory_settings';
    protected $primaryKey       = 'setting_key';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['setting_key', 'setting_value'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getValue(string $key, ?string $default = null): ?string
    {
        $row = $this->find($key);

        return $row['setting_value'] ?? $default;
    }

    public function setValue(string $key, ?string $value): bool
    {
        return (bool) $this->save([
            'setting_key'   => $key,
            'setting_value' => $value,
        ]);
    }

    public function getDiscountSettings(): array
    {
        return [
            'enable_discounts'     => $this->getValue('enable_discounts', '0') === '1',
            'global_discount_start' => $this->getValue('global_discount_start'),
            'global_discount_end'   => $this->getValue('global_discount_end'),
        ];
    }
}
