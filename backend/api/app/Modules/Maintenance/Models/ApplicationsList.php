<?php 

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationsList extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'account_affected'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'applications_list';
}