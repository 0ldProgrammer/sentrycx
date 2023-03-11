import { Component, OnInit, Input } from '@angular/core';
import { HistoricalService,TriggerService ,NotificationService } from '@app/services';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import swal from 'sweetalert2';
interface Field {
    id : string;
    name : string;
}

interface MeanOpinionScoreRecord {
    average_latency: string;
    created_at: string;
    id: number;
    jitter: number;
    mos: number;
}

@Component({
    selector: 'mos-charts-grid',
    templateUrl: './mos-charts-grid.component.html',
    styleUrls: ['./mos-charts-grid.component.css']
})
export class MOSChartsGridComponent implements OnInit {
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

    public evaluation = {
        0 : 'Bad',
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

    public records : Array<MeanOpinionScoreRecord> = [];
    public MOSData : any;
    public MOSTime : any;

    public mosLogForm = new FormGroup({
        startDate : new FormControl('', Validators.required),
        endDate : new FormControl('', Validators.required)
     });

    constructor( 
        private service : HistoricalService,
        private triggers : TriggerService,
        private notification : NotificationService
    ) { }

    ngOnInit(): void { 
        this.loadHistoricalData();
        if( this.mos <= 5 )
            this._calculateStars();
    }

   /*
    * Send a trigger to agent so send a new updated MOS record
    *
    */
   public reloadMOS(){
       const self = this;
        this.triggers.MOSRequest( this.workerID ).then( response => {
            console.log("RELOAD MOS", response);
            self.notification.alert('Reload MOS', response['msg']);
        });
   }

   /*
    * Sends a trigger to agent to stop/start auto MOS process
    *
    */
   public autoMOS( ){
       const self = this;

       this.notification.confirm('AUTO MOS', 'Are you sure you want to STOP the automated updates for MOS on his workstation?').then( result => {
            if( !result['isConfirmed'] )
                return;

            self.triggers.autoMOS( this.workerID ).then( response => {
                self.notification.alert('AUTO MOS', response['msg']);
            });
        })
   }

    public loadHistoricalData(){
        const self = this;
        const mosDetails = [];
        var mosTime = [];
        this.service.getMeanOpinionScore( this.workerID ).subscribe( response => {
            self.records = response['data'] as Array<MeanOpinionScoreRecord>;

            var i =0;
            self.records.forEach(element => {
                
                if(i < 10)
                {
                    mosDetails.push(element.mos)
                    mosTime.push(element.id)
                }
                i++;
            })
            self.isLoading = false;
        });

        this.MOSData = {
            'data'  : [mosDetails],
            'label'  : mosTime  
        }
    }


    private _calculateStars(){
        this.quality = this.evaluation[ Math.floor( this.mos ) ];

        this.starRating['full']  = Math.floor( this.mos )
        this.starRating['half']  = Math.ceil( this.mos % 1 )
        this.starRating['empty'] = this.starRating['total'] - ( this.starRating['full'] + this.starRating['half']);
    }

    /*
   * Generate MOS logs report
   * 
   */
  public extractLog(){

    let userReportTimezone = localStorage.getItem('USER_TIMEZONE_NAME');

    if( this.mosLogForm.invalid )
      return;

    let formData = this.mosLogForm.value;
    
    formData['start_date'] = `${formData['startDate'].getFullYear().toString().padStart(4, '0')}-${
        (formData['startDate'].getMonth()+1).toString().padStart(2, '0')}-${
                formData['startDate'].getDate().toString().padStart(2, '0')}`;

    formData['end_date'] = `${formData['endDate'].getFullYear().toString().padStart(4, '0')}-${
        (formData['endDate'].getMonth()+1).toString().padStart(2, '0')}-${
                formData['endDate'].getDate().toString().padStart(2, '0')}`;

    if ( formData['end_date'] < formData['start_date'] ) {
        swal.fire(
            'Error!',
            '<b>End Date</b> shoud be equal or higher than <b>Start Date</b>!',
            'error'
            );
        
            return;
    }

    const startDateTime = formData['start_date'] + ' 00:00:00';
    const endDateTime = formData['end_date'] + ' 23:59:59';

    this.service.mosDownloadReport(this.workerID, startDateTime, endDateTime, userReportTimezone ).subscribe( (data) => {
        let filename = null;
        let currentDate = new Date();
        let month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
        let date = ("0" + currentDate.getDate()).slice(-2);
        let fullDateFormat =  currentDate.getFullYear() + month + date;
        
        filename = `sentrycx_mos_${fullDateFormat}.xlsx`;
        
        let blob = new Blob([data], {
            type: 'application/octet-stream',
        });
        if (typeof window.navigator.msSaveBlob !== 'undefined') {
            window.navigator.msSaveBlob(blob, filename);
        } else {
            let blobURL = window.URL.createObjectURL(blob);
            let tempLink = document.createElement('a');
            tempLink.style.display = 'none';
            tempLink.href = blobURL;
            tempLink.download = filename;
            tempLink.click();
            window.URL.revokeObjectURL(blobURL);
        }
    });

    this.mosLogForm.reset();
  }
}
