<?php

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class UserConfig extends Model {

    protected $fillable = ['name', 'user_id', 'value'];
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_config';
}
