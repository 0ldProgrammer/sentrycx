<?php 

namespace App\Modules\Applications\Models;

use Illuminate\Database\Eloquent\Model;

class TriggerLogs extends Model {

    public $timestamps = false;
    protected $fillable = ['worker_id', 'username','workstation_name', 'triggered_event','date_triggered'];
    protected $table = 'trigger_logs';
}