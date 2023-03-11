<?php 

namespace App\Modules\HistoricalRecords\Services;

use App\Modules\HistoricalRecords\Models\LogsSecureCX;
use App\Modules\HistoricalRecords\Models\AgentSecurecxUrls;
use App\Modules\HistoricalRecords\Services\HistoricalRecordsService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SecureCXRecordsService extends HistoricalRecordsService {

    /** @var LogsMOS $model description */
    protected $model; 

    /** @var Type $query */
    protected $query;

    /** @var Array $var description */
    protected $fields = ['jitter','average_latency', 'mos', 'packet_loss', 'url', 'telnet_80', 'telnet_443'];

    /**
     *
     * Initialize necessary Model and Query 
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function __construct(){
        $this -> model = new LogsSecureCX();
        $this -> query = LogsSecureCX::query();
    }

    /**
     *
     * Setter for workerID
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function setWorkerIDToSecureCX($workerID){
        $this -> workerID = $workerID;
        return $this;
    }


    public function log( $data ){

            $this -> model::insert($data);
            $this -> addUpdateAgentSecurecxUrl($data);
    }

    /**
     * Retrieve the list of urls
     *
     * @return type
     * @throws conditon
     **/
    public function fetchUrls()
    {
        return DB::table('securecx_url')->get();
    }

    /**
     *
     * Retrieve the historical data within the day
     *
     * @return type
     * @throws conditon
     **/
    public function getSecureCXMonitoring($url){
       
        $yesterday = Carbon::yesterday();

        return $this -> query -> where('worker_id', $this -> workerID) -> where('url', $url)
            -> where('created_at', '>', $yesterday )
            -> get();
    }

    private function addUpdateAgentSecurecxUrl($data)
    {
        
        if (count($data) > 0) {
            
            foreach($data as $value) {
                
                if ($value['url'] == 'securecx-portal.concentrix.com'){
                    $data_to_update = [
                        'securecx_gate_1' => $value['url'],
                        'gate_1_telnet_80' => isset($value['telnet_80']) ? $value['telnet_80'] : '',
                        'gate_1_telnet_443' => isset($value['telnet_443']) ? $value['telnet_443'] : ''
                    ];
                    
                } else if ($value['url'] == 'securecx-frontdoor-read-aks-eastus.concentrix.com') {
                    $data_to_update = [
                        'securecx_gate_2' => $value['url'],
                        'gate_2_telnet_80' => isset($value['telnet_80']) ? $value['telnet_80'] : '',
                        'gate_2_telnet_443' => isset($value['telnet_443']) ? $value['telnet_443'] : ''
                    ];
                } else {
                    $data_to_update = [
                        'securecx_gate_3' => isset($value['url']) ? $value['url'] : '',
                        'gate_3_telnet_80' => isset($value['telnet_80']) ? $value['telnet_80'] : '',
                        'gate_3_telnet_443' => isset($value['telnet_443']) ? $value['telnet_443'] : ''
                    ];
                }
                
                AgentSecurecxUrls::updateOrCreate(
                    ['worker_id' => $value['worker_id']], 
                    $data_to_update
                );
            }
           
        }
    }

}