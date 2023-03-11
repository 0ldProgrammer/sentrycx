<?php

namespace App\Modules\HistoricalRecords\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\HistoricalRecords\Interfaces\LoggerModel;

class LogsWorkstationTrace extends Model implements LoggerModel {
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs_workstation_trace';
}
