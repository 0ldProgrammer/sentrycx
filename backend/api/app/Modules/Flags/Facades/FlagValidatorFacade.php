<?php

namespace App\Modules\Flags\Facades;
use Illuminate\Support\Facades\Facade;

class FlagValidatorFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'flag-validator'; }

}
