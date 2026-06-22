<?php

namespace App\Models;

use CodeIgniter\Model;

class SmsLogModel extends Model
{
    protected $table = 'sms_logs';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['related_order_id','mobile','pattern_code','request_payload','api_response','status','sent_at'];
}
