<?php
namespace App\Modules\Workday\Controllers;

use Illuminate\Http\Request;
use Illuminate\Container\Container;
use App\Modules\WorkstationModule\Services\AgentConnectionService;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailOnlineAgents;

class TestController extends Controller
{

    /**
     * Send My Demo Mail Example
     *
     * @return void
     */

    
    public function __construct(Container $container)
    {
        $this -> service = $container  -> get('AgentConnectionService');
    }

    public function __invoke()
    {
    	$myEmail = 'genesisrufino@yahoo.com';
    	Mail::to($myEmail)
            ->cc('patrickjoseph.quijano@concentrix.com','genesis.rufino@concentrix.com')
            ->send(new MailOnlineAgents($this->service));

    	dd("Mail Send Successfully");
    }

}