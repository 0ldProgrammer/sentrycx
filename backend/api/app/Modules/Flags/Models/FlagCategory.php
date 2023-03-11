<?php

namespace App\Modules\Flags\Models;

use Illuminate\Database\Eloquent\Model;

class FlagCategory extends Model
{
    /**
     * Get the Flag
     */
    public function user()
    {
        return $this->belongsTo('App\Modules\Flags\Models\Flag');
    }


}
