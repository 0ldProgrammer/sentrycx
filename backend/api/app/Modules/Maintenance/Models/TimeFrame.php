<?php 

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class TimeFrame extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account', 'data_to_track', 'start_date', 'end_date'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'time_frames';
}