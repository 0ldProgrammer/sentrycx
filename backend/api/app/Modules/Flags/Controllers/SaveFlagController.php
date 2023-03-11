<?php

namespace App\Modules\Flags\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use App\Modules\Flags\Facades\FlagValidatorFacade;

class SaveFlagController extends Controller {

    /** @var Type $flagService Instance of FlagService */
    private $flagService = null;

    /**
     * Constructor Method
     *
     * Define the Injections/Services here needed by this coontroller
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(Container $container ){
        $this -> flagService = $container -> get ('FlagService');
    }

    /**
     * Invoke Function
     *
     * Handles the route function
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __invoke(Request $request ) {
        $this -> validate( $request, FlagValidatorFacade::rules() );

        $form_data = $request -> all();

        return  $this -> flagService -> save ( $form_data );
    }
}
