<?php

namespace App\Modules\Maintenance\Services;

use Illuminate\Support\Facades\DB;

class OrganizationService {

    /**
     *
     * Retrieve all the possible sites
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function getSites(){
        return DB::table('cnx_employees')
            -> select('location_name')
            -> distinct()
            -> orderBy('location_name', 'ASC')
            -> where('location_name', '<>', null)
            -> get();

    }

    public function getAccounts(){
        return DB::table('cnx_employees')
            -> select('msa_client')
            -> distinct()
            -> orderBy('msa_client', 'ASC')
            -> where('msa_client', '<>', null)
            -> get();

    }
}
