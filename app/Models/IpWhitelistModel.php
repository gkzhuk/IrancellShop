<?php

namespace App\Models;

use CodeIgniter\Model;

class IpWhitelistModel extends Model
{
    protected $table            = 'api_ip_whitelist';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['ip_address', 'label', 'is_active'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'ip_address' => 'required|max_length[45]|is_unique[api_ip_whitelist.ip_address,id,{id}]',
        'is_active'  => 'in_list[0,1]',
    ];

    /**
     * Check whether a given IP address is in the active whitelist.
     */
    public function isAllowed(string $ip): bool
    {
        return $this->where('ip_address', $ip)
                    ->where('is_active', 1)
                    ->countAllResults() > 0;
    }
}
