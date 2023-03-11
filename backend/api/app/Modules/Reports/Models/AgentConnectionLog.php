<?php 

namespace App\Modules\Reports\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class AgentConnectionLog extends Model  {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agent_connections_log';
}
