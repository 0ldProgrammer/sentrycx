<?php 

namespace App\Modules\WorkstationModule\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class AgentLocation extends Model  {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'worker_id', 'country', 'country_code', 'neighbourhood', 'region', 'city', 'zip_code', 'latitude', 'longitude'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agent_location';
}
