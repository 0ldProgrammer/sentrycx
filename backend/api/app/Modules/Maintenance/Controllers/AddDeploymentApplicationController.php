<?php
namespace App\Modules\Maintenance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use App\Modules\Maintenance\Models\Installer;
use App\Modules\Maintenance\Models\DesktopApplicationList;
use App\Modules\Maintenance\Events\AgentDeploymentApplicationBroadcast;
use Illuminate\Support\Facades\Storage;

class AddDeploymentApplicationController extends BaseController {

    /** @var App\Modules\Maintenance\Services\MaintenanceService  $maintenanceService description */
    protected $maintenanceService;

    /**
     * Constructor Method
     * Define constructor dependencies here
     *
     * @return void
     **/
    public function __construct(Container $container ){
        $this -> maintenanceService = $container -> get ('MaintenanceService');
    }
    

    /**
     * Handles the adding of application
     *
     * @param Request $request
     * @return Response
     * @throws conditon
     **/
    public function __invoke( Request $request)
    {
        
        return $this -> addEditDeploymentApplication($request);
    }

    public function addEditDeploymentApplication($request)
    {
        $currentUTCDate = Carbon::now('UTC')->format('Y-m-d');

        $desktop_data = DesktopApplicationList::updateOrCreate(
            ['id' => $request['id']],
            [
                'name' => $request['name'],
                'description' => $request['description'],
                'is_self_install' => $request['is_self_install'],
                'is_ps' => $request['is_ps'],
                'ps_or_dl' => $request['ps_or_dl'],
                'arguments' => $request['arguments'],
                'execution_date' => $request['execution_date']
                // 'time' => $request['time']
            ]
        );
        
        if (!isset($request['fileUpload'])){
             // create or update new application installer
            $file_name = $request->file('fileKey');
            $base_filename = str_replace(' ', '_', pathinfo($file_name->getClientOriginalName(), PATHINFO_FILENAME));
            Installer::updateOrCreate(
            ['application_id' => $desktop_data['id']],
            [
                'base_filename' => $base_filename . '_' . Carbon::now()->timestamp,
                'directory' => '/installers',
                'disk' => config('filesystems.default'),
                'extension'=> $file_name->getClientOriginalExtension(),
                'mime_type'=>$file_name->getMimeType(),
            ]);

            $file_name->storeAs('/installers', $base_filename . '_' . Carbon::now()->timestamp . '.' .$file_name->getClientOriginalExtension(),'public');
        }

        if($currentUTCDate == $request['execution_date']){
            if ($desktop_data) {
                $obj_data = (object) $desktop_data;
                
                $data = DesktopApplicationList::query()
                    ->addSelect('*')
                    ->addSelect('desktop_application_list.id as desktop_id', 'installers.id as installer_id')
                    ->leftJoin('installers', 'installers.application_id', '=', 'desktop_application_list.id')
                    ->where('desktop_application_list.id', $obj_data->id )->first();
                
                $this -> _dispatch($data);
    
                return ['status' => 'OK', 'data'=>  $obj_data];
            }
        }
        
    }

    public function _dispatch($data){
        $session_ids = $this -> maintenanceService -> getAllAgentSession();

        $download_path = Storage::disk($data->disk)->url($data->directory.'/'.$data->base_filename.'.'.$data->extension);

        foreach($session_ids as $session_id) {
            event( new AgentDeploymentApplicationBroadcast($session_id->session_id, $data, $download_path ));
        }
    }
}
