<?php 

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class UserSoftwareUpdate extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'worker_id', 'os', 'update_id', 'description', 'is_installed', 'support_url', 'patch_name', 'is_expired'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_software_updates';
}