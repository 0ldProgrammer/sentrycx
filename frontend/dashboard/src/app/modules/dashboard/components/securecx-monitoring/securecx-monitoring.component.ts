import { Component, OnInit, Input } from '@angular/core';
import { HistoricalService,TriggerService ,NotificationService } from '@app/services';

interface Field {
    id : string;
    name : string;
}

interface SecureCXMonitoringRecord {
    average_latency: string;
    created_at: string;
    id: number;
    jitter: number;
    mos: number;
}

interface SecureCXUrls {
    url: string;
    id: number;
}

@Component({
    selector: 'securecx-monitoring',
    templateUrl: './securecx-monitoring.component.html'
})
export class SecureCXMonitoringComponent implements OnInit {
    @Input('data-agent') agent : String;
    @Input('data-packet-loss') packetLoss : number;
    @Input('data-average-latency') averageLatency : number;
    @Input('data-jitter') jitter : number;
    @Input('data-mos') mos : number;
    @Input('data-worker-id') workerID : String;
    @Input('data-readonly') readonly : Boolean = false;

    public HISTORICAL_FIELDS : Array<Field> =  [
        {id: 'average_latency', name:' AVG Latency' },
        {id: 'jitter', name : 'Jitter'},
        {id: 'packet_loss', name :'Packet Loss' },
        {id: 'mos', name : 'MOS' }
    ];

    public EVALUATION_LIST = [
        { rating : 1, quality : 'Bad', class : 'text-danger' },
        { rating : 2, quality : 'Poor', class : 'text-danger' },
        { rating : 3, quality : 'Fair', class : 'text-warning' },
        { rating : 4, quality : 'Good', class : 'text-success', },
        { rating : 5, quality : 'Excellent', class : 'text-success'},
    ];

    public isLoading : Boolean = true;

    public subUrl : String;
    public initialUrl : String;

    public evaluation = {
        0 : 'No MOS extracted',
        1 : 'Bad',
        2 : 'Poor',
        3 : 'Fair',
        4 : 'Good',
        5 : 'Excellent'
    }

    public quality = 'Checking';

    public starColorRef = {
        0 : 'text-disabled',
        1 : 'text-danger',
        2 : 'text-warning',
        3 : 'text-info',
        4 : 'text-success',
        5 : 'text-success'
    }

    starRating = {
        total : 5,
        empty : 0,
        half  : 0,
        full  : 0
    }

    public records : Array<SecureCXMonitoringRecord> = [];

    public urls : Array<SecureCXUrls> = [];

    constructor( 
        private service : HistoricalService,
        private triggers : TriggerService,
        private notification : NotificationService
    ) { }

    ngOnInit(): void { 
        this._loadHistoricalData();
        this.listOfUrls();

        if( this.mos <= 5 )
            this._calculateStars();
    }

   /*
    * Send a trigger to agent so send a new updated MOS record
    *
    */
//    public reloadMOS(){
//        const self = this;
//         this.triggers.MOSRequest( this.workerID ).then( response => {
//             console.log("RELOAD Secure CX", response);
//             self.notification.alert('Reload Secure CX', response['msg']);
//         });
//    }

   public listOfUrls(){
    const self = this;
    this.service.getSecureCXUrls().subscribe( response => {
        self.initialUrl = response[0];
        self.setUrl(self.initialUrl);
        self.urls = response as Array<SecureCXUrls>;
        self.isLoading = false;
    });
   }

    /*
     * Change the url
     */
    public setUrl( urlName ) {
        this.subUrl =  urlName.url;
        this._loadHistoricalData();
    }

    /*
    * Sends a trigger to agent to stop/start auto MOS process
    *
    */

//    public autoMOS( ){
//        const self = this;

//        this.notification.confirm('AUTO MOS', 'Are you sure you want to STOP the automated updates for MOS on his workstation?').then( result => {
//             if( !result['isConfirmed'] )
//                 return;

//             self.triggers.autoMOS( this.workerID ).then( response => {
//                 self.notification.alert('AUTO MOS', response['msg']);
//             });
//         })
//    }

    private _loadHistoricalData(){
        const self = this;
        this.service.getSecureCXMonitoring( this.workerID, this.subUrl ).subscribe( response => {
            self.records = response['data'] as Array<SecureCXMonitoringRecord>;
            self.isLoading = false;
        });
    }

    private _calculateStars(){
        this.quality = this.evaluation[ Math.floor( this.mos ) ];

        this.starRating['full']  = Math.floor( this.mos )
        this.starRating['half']  = Math.ceil( this.mos % 1 )
        this.starRating['empty'] = this.starRating['total'] - ( this.starRating['full'] + this.starRating['half']);
    }
}
