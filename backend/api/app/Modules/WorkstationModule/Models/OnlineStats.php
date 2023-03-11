<?php 

namespace App\Modules\WorkstationModule\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class OnlineStats extends Model  {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'online_stats';
}
