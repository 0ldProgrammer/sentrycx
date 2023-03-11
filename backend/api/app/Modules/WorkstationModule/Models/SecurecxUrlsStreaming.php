<?php 

namespace App\Modules\WorkstationModule\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class SecurecxUrlsStreaming extends Model  {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'worker_id', 'streaming_primary_url', 'streaming_primary_host_name', 'streaming_primary_port', 'streaming_primary_telnet_result',
        'streaming_secondary_url', 'streaming_secondary_host_name', 'streaming_secondary_port', 'streaming_secondary_telnet_result',
        'status_primary_url', 'status_primary_host_name', 'status_primary_port', 'status_primary_telnet_result',
        'status_secondary_url', 'status_secondary_host_name', 'status_secondary_port', 'status_secondary_telnet_result'
    ];
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'securecx_urls_streaming';
}
