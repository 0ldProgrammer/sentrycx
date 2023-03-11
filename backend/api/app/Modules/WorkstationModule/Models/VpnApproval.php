<?php 

namespace App\Modules\WorkstationModule\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class VpnApproval extends Model  {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'worker_id', 'name', 'email', 'status', 'action_taken_by', 'remarks', 'workstation', 'action_taken_at'
    ];
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vpn_approval';
}
