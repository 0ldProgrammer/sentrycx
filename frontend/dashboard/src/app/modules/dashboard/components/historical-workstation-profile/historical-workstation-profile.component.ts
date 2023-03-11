import { 
    Component, 
    OnInit,
    Input,
    Output,
    EventEmitter
} from '@angular/core';

import { IPaginate } from '@app/interfaces';
import { PaginateFactory } from '@app/factory';
import { HistoricalService } from '@app/services';

interface IProfile {
    'worker_id' : string;
    'date_created' : string;
    'host_ip_address' : string;
    'station_number' : string;
    'subnet' : string;
    'DNS_1' : string;
    'DNS_2' : string;
    'VLAN' : string;
    'download_speed' : string;
    'gateway' : string;
    'upload_speed' : string;
    'ISP' : string;
}

@Component({
    selector: 'historical-workstation-profile',
    templateUrl: './historical-workstation-profile.component.html',
    styleUrls: ['./historical-workstation-profile.component.css']
})
export class HistoricalWorkstationProfileComponent implements OnInit {
    @Input('data-agent-name') agentName : String ;
    @Input('data-profile')  profile : IProfile;
    @Input('data-worker-id') workerID : String;
    @Input('data-historical') isHistorical : Boolean = false; 
    @Input('data-speedtest') speedtest : Array<any>;
    public paginate : IPaginate = PaginateFactory.init();
    public historicalData : Array<IProfile> = [];
    public speedtestParsed = {
        downloadSpeed : '',
        uploadSpeed   : ''
    };


    constructor( private service : HistoricalService ) { }

    ngOnInit(): void { 
        this._parseSpeedtest();
    }

    /*
     * Parse the speedtest result to textarea readable format
     */ 
    private _parseSpeedtest(){
        const self = this;

        this.speedtest.forEach( (result ) => {
            self.speedtestParsed.downloadSpeed += `${result['download_speed']} \n`;
            self.speedtestParsed.uploadSpeed += `${result['upload_speed']} \n`;
        });
    }

    /*
    * Creates an array based on range
    */
    public range(n: number): any[] {
        return Array(n);
    }

    /*
     * Update the current profile data to show
     */ 
    public updateData( pageNo ){
        this.paginate.currentPage = pageNo;
        this.profile = this.historicalData[ pageNo - 1 ];
    }
}
