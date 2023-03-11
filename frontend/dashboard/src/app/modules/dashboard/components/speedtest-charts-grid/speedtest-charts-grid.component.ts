import { Component, OnInit, Input } from '@angular/core';
import { HistoricalService  } from '@app/services';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import swal from 'sweetalert2';

interface IField {
    id : string;
    name : string;
}

interface ISpeedtestRecord {
    download_speed : number;
    upload_speed : number;
    connection_type : string;
    id : number;
}

@Component({
    selector: 'speedtest-charts-grid',
    templateUrl: './speedtest-charts-grid.component.html',
    styleUrls: ['./speedtest-charts-grid.component.css']
})
export class SpeedtestChartsGridComponent implements OnInit {
    @Input('data-agent') agent : String;
    @Input('data-upload-speed') uploadSpeed : String;
    @Input('data-download-speed') downloadSpeed : String;
    @Input('data-isp') isp : String;
    @Input('data-worker-id') workerID : String;
    public records : Array<ISpeedtestRecord> = [];
    public isLoading : Boolean = true;
    public speedTestData : any;


    public HISTORICAL_FIELDS : Array<IField> =  [
        {id: 'download_speed', name:' DOWN Speed' },
        {id: 'upload_speed', name : 'UP Speed'},
        {id: 'connection_type', name :'Network' },
    ];

    public speedtestLogForm = new FormGroup({
        startDate : new FormControl('', Validators.required),
        endDate : new FormControl('', Validators.required)
     });

    constructor( private service : HistoricalService) { }

    ngOnInit(): void {
        const self = this;
        const label = [];
        const upSpeedTestDetails = [];
        const downSpeedTestDetails = [];
        this.service.getSpeedtest( this.workerID ).subscribe ( response => {
            self.isLoading = false;
            self.records = response['data'];

            var i =0;
            self.records.forEach(element => {
                
                if(i < 10)
                {
                    upSpeedTestDetails.push(Number(`${element.upload_speed}`))
                    downSpeedTestDetails.push(Number(`${element.download_speed}`))
                    label.push(element.id)
                }
                i++;
            })
        });

        this.speedTestData = {
            'data'  : [upSpeedTestDetails,downSpeedTestDetails],
            'label' : label
        }

        
     }

   /*
   * Generate MOS logs report
   * 
   */
   public extractLog(){

    let userReportTimezone = localStorage.getItem('USER_TIMEZONE_NAME');

    if( this.speedtestLogForm.invalid )
      return;

    let formData = this.speedtestLogForm.value;
    
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

    this.service.speedtestDownloadReport(this.workerID, startDateTime, endDateTime, userReportTimezone ).subscribe( (data) => {
        let filename = null;
        let currentDate = new Date();
        let month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
        let date = ("0" + currentDate.getDate()).slice(-2);
        let fullDateFormat =  currentDate.getFullYear() + month + date;
        
        filename = `sentrycx_speedtest_${fullDateFormat}.xlsx`;
        
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

    this.speedtestLogForm.reset();
  }
    
}
