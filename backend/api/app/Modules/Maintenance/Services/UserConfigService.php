<?php 

namespace App\Modules\Maintenance\Services;

use App\Modules\Maintenance\Models\UserConfig;

class UserConfigService {

    /** @var String $userID  */
    protected $userID;

    /**
     *
     * Setter for userID
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function setUser( $userID ){
        $this -> userID = $userID;
    }

    /**
     *
     * Retrieve all the user config of the user
     *
     * @return type
     * @throws conditon
     **/
    public function all(){
        return UserConfig::where('user_id',  $this -> userID ) -> get();
    }

    /**
     *
     * Retrieve the configuration of the user
     *
     * @param String $configName
     * @return type
     * @throws conditon
     **/
    public function get($configName){
        $config = UserConfig::where('name', $configName)
            -> where('user_id', $this -> userID )
            -> first();

        if( !$config )
            return null;

        return json_decode( $config->value );
    }

    /**
     * 
     * Sets the configuration of the user
     *
     * @param String $configName
     * @param Array $data
     * @return type
     * @throws conditon
     **/
    public function set($configName, $data){
        return UserConfig::updateOrCreate(
            ['name' => $configName, 'user_id' => $this -> userID  ],
            ['value' => json_encode( $data ) ]
        );
    }



}