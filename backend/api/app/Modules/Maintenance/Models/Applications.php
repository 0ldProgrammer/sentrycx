<?php 

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class Applications extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'url','ip','account_id', 'account','date_created','is_active','hosted','notes','category_id',
        'code_id','threshold','is_monitoring','is_loaded'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'applications';

    public $timestamps = false;
}