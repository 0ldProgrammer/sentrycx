<?php 

namespace App\Modules\WorkstationModule\Services;

use Illuminate\Support\Facades\DB;

// TODO : Move this into Maintenance Module
class AccountsService {
    

    /**
     *
     * Retrieve the list of Accounts
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function query($page = 1, $value = "", $isActive="", $mediaCheck="", $per_page = 20, $has_securecx="" ){
        $query = DB::table('accounts') -> orderBy('name', 'ASC');

        if($value!="")
            $query->where('name', 'like', "%$value%");    

        if($isActive!="")
            $query->where('is_active', $isActive);

        if($mediaCheck!="")
            $query->where('need_device_check', $mediaCheck);

        if($has_securecx!="")
            $query->where('has_securecx', $has_securecx);
        

        return $query -> paginate($per_page, ['*'], 'page', $page );
    }

    /**
     *
     * Save the account details. 
     * If ID was provided, it will update the existing record
     *
     * @param Array $data
     * @param Int $id
     * @return type
     * @throws conditon
     **/
    public function save($data, $id = 0){
        $data_check_sites_devices = false;
        if ($data['need_device_check']){
            $data_check_sites_devices = $data['check_sites_devices'];
        }

        $query = DB::table('accounts');
        $account_details = [
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'need_device_check' => $data['need_device_check'],
            'need_hostfile_url' => $data['need_hostfile_url'],
            'check_device_url'  => getenv('APP_URL') . "client/device-check",
            'check_hostfile_url' => getenv('APP_URL') . "workstation/hostfile/" . $data['name'],
            'has_securecx' => $data['has_securecx'],
            'check_sites_devices' => $data_check_sites_devices
        ];

        if( $id )
            return $query -> where('id', $id)->update( $account_details );

        return $query -> insert( $account_details );
    }

    /**
     *
     * Delete the account based on ID
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function delete($id = 0){
        return DB::table('accounts')->where('id', $id)->delete();
    }

    /**
     *
     * Update centralized hostfile url assigned in accounts
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function setHostfile($account, $hostfile_url ){
        return DB::table('accounts')
            ->where('name', $account)
            ->update([
                'need_hostfile_url' => TRUE,
                'check_hostfile_url' => $hostfile_url
            ]);
    }
}