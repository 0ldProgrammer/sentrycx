<?php

namespace App\Modules\Flags\Helpers;

use App\Modules\Flags\Helpers\Validator;

class FlagValidator extends Validator {

    /** @var Type $rules description */
    protected $rules = [
        'category_id' => 'required|numeric',
        'code_id'     => 'required|numeric',
        'employee_id' => 'required',
        'country'     => 'required',
        'account'     => 'required',
        'isp'         => 'required',
        'location'    => 'required',
    ];

}
