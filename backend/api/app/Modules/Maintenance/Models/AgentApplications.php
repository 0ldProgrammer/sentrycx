<?php 

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class AgentApplications extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'worker_id', 'application_id', 'account', 'location'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agent_applications';
}