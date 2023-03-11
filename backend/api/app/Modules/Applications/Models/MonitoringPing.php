<?php

namespace App\Modules\Applications\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringPing extends Model {
     /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $fillable = [
        'worker_id',
        'monitoring_id',
        'is_reference',
        'is_mos',
        'roundtrip_sequence',
        'hostname',
        'address',
        'reply_status',
        'reply_address',
        'reply_buffer',
        'reply_roundtrip_time',
        'reply_ttl'
    ];

    protected $table = 'monitoring_ping';
}
