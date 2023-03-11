<?php

namespace App\Modules\Applications\Models;

use Illuminate\Database\Eloquent\Model;

class AgentLocation extends Model {
     /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $fillable = [
        'worker_id',
        'country',
        'country_code',
        'neighbourhood',
        'region',
        'city',
        'zip_code',
        'latitude',
        'longitude',
        'deleted_at',
        'created_at',
        'updated_at'
    ];
    protected $table = 'agent_location';
}
