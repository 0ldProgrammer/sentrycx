<?php 

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class SmartReportType extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'report_type'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'smart_report_types';
}