<?php

namespace App\Modules\Applications\Models;

use Illuminate\Database\Eloquent\Model;

class HardwareInfo extends Model {
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $fillable = [
        'worker_id',
        'station_number',
        'gpu',
        'disk_drive',
        'processor',
        'os',
        'network_interface',
        'sound_card',
        'printer',
        'monitor',   
        'camera',
        'keyboard',
        'mouse',
        'installed_apps',
        'ram',
        'memory',
        'mother_board'
    ];

    protected $table = 'hardware_info';
}
