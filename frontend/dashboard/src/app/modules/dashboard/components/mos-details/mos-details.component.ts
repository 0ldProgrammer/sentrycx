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
    selector: 'mos-details',
    templateUrl: './mos-details.component.html',
    styleUrls: ['./mos-details.component.css']
})
export class MOSDetailsComponent implements OnInit {
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
}
