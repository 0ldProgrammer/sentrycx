<?php 

namespace App\Modules\Reports\Models;

use Illuminate\Database\Eloquent\Model;

class MetricThreshold extends Model  {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'report_id', 'account_id', 'name', 'threshold'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'metrics_thresholds';
}
