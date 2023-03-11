<?php 

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class LogsSoftwareUpdate extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'worker_id', 'update_id', 'description', 'is_installed', 'patch_name'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs_software_updates';
}