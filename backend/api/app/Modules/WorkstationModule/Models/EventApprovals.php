<?php 

namespace App\Modules\WorkstationModule\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class EventApprovals extends Model  {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_approvals';
}
