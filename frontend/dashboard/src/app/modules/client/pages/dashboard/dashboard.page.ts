import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { WorkstationService } from '@app/services';

interface IPill {
    href : string;
    name : string;
}

interface IWorkstation {
    worker_id: string;
    agent_name : string;
    packet_loss: string;
    average_latency: string;
    jitter: string;
    mos: string;
}

@Component({
    selector: 'client-dashboard',
    templateUrl: './dashboard.page.html',
    styleUrls: ['./dashboard.page.css']
})
export class DashboardPage implements OnInit {
    public NAVIGATION_PILLS : Array<IPill> = [
        { href : '#agent-profile', name : 'Agent Profile' },
        { href : '#mean-opinion-score', name : 'Mean Opinion Score' }
    ];

    public hardwareInfo : IWorkstation;

    public currentPill = '#agent-profile';

    public workerID : string; 

    public hasLoaded : Boolean = false;

    constructor( 
        private route : ActivatedRoute,
        private service : WorkstationService 
    ) { }

    ngOnInit(): void { 
        const self = this;
        this.route.queryParamMap.subscribe(( query ) => {
            const workerID = query['params']['workerID']; 
            if( !workerID ) return;

            self.workerID = workerID;
            self._loadWorkstation();

        });
    }


    private _loadWorkstation(){
        const self = this;
        this.service.getAgentWorkstation( this.workerID ).subscribe( response => {
            self.hardwareInfo = response['data'][0];
            self.hasLoaded = true;
        });
    }


}
