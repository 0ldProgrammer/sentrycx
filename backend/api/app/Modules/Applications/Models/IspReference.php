<?php

namespace App\Modules\Applications\Models;

use Illuminate\Database\Eloquent\Model;


class IspReference extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'isp_code', 'isp_name'
    ];
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'isp_reference';
}
