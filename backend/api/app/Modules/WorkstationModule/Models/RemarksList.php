<?php 

namespace App\Modules\WorkstationModule\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class RemarksList extends Model  {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'remarks'
    ];
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'remarks_list';
}
