<?php 

namespace App\Modules\WorkstationModule\Models;

use Illuminate\Database\Eloquent\Model;

class VersionUpdateLogs extends Model {

    protected $fillable = ['worker_id', 'account','no_attempts', 'session_id'];
    protected $table = 'version_update_logs';
}