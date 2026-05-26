<?php

namespace App\Models;

use CodeIgniter\Model;

class SmsLogModel extends Model
{
    protected $table = 'sms_logs';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['mobile','message','status','api_response','related_order_id','event_key','sent_at'];
}
