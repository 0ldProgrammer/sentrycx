<?php

namespace App\Modules\HistoricalRecords\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\HistoricalRecords\Interfaces\LoggerModel;

class LogsSecureCX extends Model implements LoggerModel {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_at', 'updated_at'
    ];

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs_securecx';
    
    public $timestamps = true;
}
