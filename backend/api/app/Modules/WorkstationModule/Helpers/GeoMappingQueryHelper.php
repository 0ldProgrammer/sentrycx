<?php


namespace App\Modules\WorkstationModule\Helpers;


class GeoMappingQueryHelper {

    /**
     * Generates conditional statement
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param Array $conditions
     * @return \Illuminate\Database\Query\Builder $query
     * @throws conditon
     **/
    public static function condition($query, $conditions){

        $field_mapping = [
            'city' => 'al.city',
            'account'  => 'agent_connections.account',
            'country' => 'al.country',
            'location' => 'agent_connections.location'
        ];

        foreach( $conditions as $field_ref => $value  ){
            $field = $field_mapping[$field_ref];

            if( !$value &&  !in_array($field, $boolean_fields)) continue;

            if( is_array($value)){
                $query -> whereIn( $field, $value );
                continue;
            }

            $query -> where($field, $value );
        }

        return $query;
    }
}
