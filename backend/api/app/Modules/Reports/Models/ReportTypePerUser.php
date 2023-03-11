<?php 

namespace App\Modules\Reports\Models;

use Illuminate\Database\Eloquent\Model;

class ReportTypePerUser extends Model  {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'report_type_id', 'email', 'created_at', 'updated_at'
    ];


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_types_per_user';
}
