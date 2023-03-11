<?php

namespace App\Modules\Flags\Models;

use Illuminate\Database\Eloquent\Model;

class Flag extends Model
{
    /**
     * Left join the category
     *
     * @return type
     * @throws conditon
     **/
    public function category() {
        return $this -> hasOne("App\Modules\Flags\Models\FlagCategory","id", "category_id");
    }
}
