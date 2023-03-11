<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\LoggerModel;

class LogsMOS extends Model implements LoggerModel {
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs_mos';
}
