<?php 

namespace App\Modules\WorkstationModule\Traits;

trait RequestParserTrait {

     /**
     *
     * Checks the array if the propery exists, if null, return null as well
     * This avoid getting index error
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function extractFromData( $data, $property_name, $default = ''){
        if( !array_key_exists( $property_name , $data ) ) 
            return $default;

        return $data[ $property_name ];
    }
}