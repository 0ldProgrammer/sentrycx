<?php 

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class AuxList extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'color', 'aux_status'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'aux_list';
}