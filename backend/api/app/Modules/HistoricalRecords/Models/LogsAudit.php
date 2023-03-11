<?php

namespace App\Modules\HistoricalRecords\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\HistoricalRecords\Interfaces\LoggerModel;

class LogsAudit extends Model implements LoggerModel {
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'audit_logs';
}
