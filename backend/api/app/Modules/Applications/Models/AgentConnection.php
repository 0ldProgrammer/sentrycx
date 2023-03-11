<?php

namespace App\Modules\Applications\Models;

use Illuminate\Database\Eloquent\Model;

class AgentConnection extends Model {
     /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $fillable = [
        'worker_id',
        'session_id'
    ];

    protected $table = 'agent_connections';
}
