<?php

namespace App\Modules\HistoricalRecords\Models;

use Illuminate\Database\Eloquent\Model;

class AgentSecurecxUrls extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'worker_id', 'securecx_gate_1', 'securecx_gate_2', 'securecx_gate_3', 
        'gate_1_telnet_80', 'gate_1_telnet_443', 'gate_2_telnet_80', 'gate_2_telnet_443',
        'gate_3_telnet_80', 'gate_3_telnet_443'
    ];

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agent_securecx_urls';
}
