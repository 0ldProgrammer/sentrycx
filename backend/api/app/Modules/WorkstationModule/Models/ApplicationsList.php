<?php 

namespace App\Modules\WorkstationModule\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class ApplicationsList extends Model  {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'applications_list as al';
}
