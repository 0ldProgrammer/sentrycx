<?php


namespace App\Modules\WorkstationModule\Helpers;


class ConnectedAgentsQueryHelper {

    const SPEEDTEST_THRESHOLD = 10;

    const MOS_THRESHOLD = 3.6;


    /**
     * Generates conditional statement
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param Array $conditions
     * @return \Illuminate\Database\Query\Builder $query
     * @throws conditon
     **/
    public static function condition($query, $conditions){
        $boolean_fields = ['is_admin', 'is_active'];

        $field_mapping = [
            'location' => 'ac.location',
            'account'  => 'ac.account',
            'agent_name' => 'ac.agent_name',
            'country'  => 'ac.country',
            'ISP'      => 'wp.ISP',
            'VLAN'     => 'wp.VLAN',
            'DNS_1'    => 'wp.DNS_1',
            'DNS_2'    => 'wp.DNS_2',
            'subnet'   => 'wp.subnet',
            'is_admin' => 'ac.is_admin',
            'connection' => 'ac.is_active',
            'aux_status' => 'ac.aux_status',
            'speedtest'  => 'wp.download_speed',
            'ram' => 'wp.ram',
            'disk' => 'wp.disk',
            'cpu' => 'wp.cpu'
        ];

        foreach( $conditions as $field_ref => $value  ){
            $field = $field_mapping[$field_ref];

            if( $field_ref == 'connection' ) {
                if( $value == 'offline' )
                    $query -> where($field, FALSE );
                else if( $value == 'online' )
                    $query -> where($field, TRUE);
                continue;
            }

            // TODO : REFRACTOR THIS AND REFRAIN FROM 
            //        LOTS OF IF STATEMENT
            if( $field_ref == 'aux_status' && $value == 'ALL')  
                continue;

            if( $field_ref == 'speedtest' && $value == 'threshold')  {
                $query -> where( function( $q ) {
                    $q -> where(function ($que) {
                        $que -> whereRaw('wp.download_speed < 10 and wp.download_speed >= 1');
                    });

                    $q -> orWhere(function ($que) {
                        $que -> whereRaw('wp.upload_speed < 5 and wp.upload_speed >= 1');
                    });

                    $q -> orWhere(function ($que) {
                        $que -> whereRaw('wp.mos < 3.6 and wp.mos >= 1');
                    });
                });
                continue;
            }

            else if( $field_ref == 'speedtest' )  
                continue;



            if( !$value &&  !in_array($field, $boolean_fields)) continue;

            if( $field_ref == 'agent_name' ){
                $field_value = implode('', $value);
                $query -> where($field, 'like', "%{$field_value}%");
                continue;
            }

            if( is_array($value)){
                $query -> whereIn( $field, $value );
                continue;
            }

            $query -> where($field, $value );
        }

        // #$query -> where('is_active', TRUE);
        if( !array_key_exists( 'connection', $conditions ))
            $query -> where('ac.is_active', TRUE);

        // echo $query->toSql();
        // die();

        return $query;
    }
}
