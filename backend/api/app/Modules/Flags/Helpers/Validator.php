<?php

namespace App\Modules\Flags\Helpers;


class Validator {


    /** @var Type $rules description */
    protected $rules = [];

    /**
     * Returns the validationRule
     *
     * @return type
     * @throws conditon
     **/
    public function rules()
    {
        return $this -> rules;
    }
}
