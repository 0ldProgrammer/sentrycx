<?php

namespace App\Modules\Applications\Models;

use Illuminate\Database\Eloquent\Model;

class AgentApplications extends Model {
     /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $fillable = [
        'worker_id',
        'application_id',
        'type',
        'account',
        'location',
        'installed_date'
    ];

    protected $table = 'agent_applications';
}
