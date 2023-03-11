<?php

namespace App\Modules\Applications\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringTraceroute extends Model {
     /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $fillable = [
        'worker_id',
        'monitoring_id',
        'is_reference',
        'is_mtr',
        'host_name',
        'address',
        'hop_sequence',
        'roundtrip_sequence',
        'reply_hostname',
        'reply_address',
        'reply_roundtrip_time',
        'reply_ttl'
    ];

    protected $table = 'monitoring_traceroute';
}
