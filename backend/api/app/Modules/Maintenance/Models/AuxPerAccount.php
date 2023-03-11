<?php 

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class AuxPerAccount extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'aux_list_id', 'account'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'aux_per_account';
}