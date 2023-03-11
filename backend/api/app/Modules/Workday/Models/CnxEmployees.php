<?php 

namespace App\Modules\Workday\Models;

use Illuminate\Database\Eloquent\Model;

class CnxEmployees extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cnx_employees as ce';

    public $timestamps = false;
}


