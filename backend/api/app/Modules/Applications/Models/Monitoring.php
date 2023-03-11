<?php

namespace App\Modules\Applications\Models;

use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model {
     /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $fillable = [
        'worker_id',
        'workstation_id',
        'application',
        'type',
        'ram',
        'memory',
        'average_latency',
        'packet_loss',
        'jitter',
        'mos'
    ];

    protected $table = 'monitoring';
}
