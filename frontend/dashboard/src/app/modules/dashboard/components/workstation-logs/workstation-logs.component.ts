import { Component, OnInit, Input } from '@angular/core';
import { HistoricalService } from '@app/services';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import swal from 'sweetalert2';

interface IField {
    id : string;
    name : string;
    class : string;
}

interface IWorkstationRecord {
    created_at : string,
    host_ip_address : string,
    subnet : string,
    download_speed : string,
    upload_speed : string,
    gateway : string,
    DNS_1 : string
}

@Component({
    selector: 'workstation-logs',
    templateUrl: './workstation-logs.component.html'
})
export class WorkstationLogsComponent implements OnInit {
    @Input('data-worker-id') workerID : String;

    public isLoading : Boolean = true;
    public records : Array<IWorkstationRecord> = [];
    public HISTORICAL_FIELDS : Array<IField> =  [
        {id: 'subnet', name : 'Subnet', class : 'text-left', },
        {id: 'gateway', name : 'Gateway', class : 'text-left', },
        {id: 'DNS_1', name : 'DNS', class : 'text-left' },
        {id: 'VLAN', name: 'VLAN', class :'text-left' },
        {id: 'ISP', name: 'ISP', class : 'text-left' }
        // {id: 'download_speed', name:' DOWN Speed' },
        // {id: 'upload_speed', name : 'UP Speed'},
    ];

    public workstationHistoryLogForm = new FormGroup({
        startDate : new FormControl('', Validators.required),
        endDate : new FormControl('', Validators.required)
     });

    constructor( 
        private service : HistoricalService 
    ) { }

    ngOnInit(): void { 
        this.loadData();

    }


    public loadData(){
        const self = this;
        this.service.getWorkstationProfile( this.workerID ).subscribe( response => {
            self.records = response as Array<IWorkstationRecord> ;
            self.isLoading = false;
        });
    }

    /*
   * Generate Workstation History logs report
   * 
   */
  public extractLog(){

    let userReportTimezone = localStorage.getItem('USER_TIMEZONE_NAME');

    if( this.workstationHistoryLogForm.invalid )
      return;

    let formData = this.workstationHistoryLogForm.value;

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
    this.isLoading = true;
    this.service.workstationHistoryDownloadReport(this.workerID, startDateTime, endDateTime, userReportTimezone ).subscribe( (data) => {

        if (data.type == "application/json") {
            swal.fire(
                'Error!',
                'Lost Connection to Server',
                'error'
                );
            this.isLoading = false;
        } else {
            let filename = null;
            let currentDate = new Date();
            let month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
            let date = ("0" + currentDate.getDate()).slice(-2);
            let fullDateFormat =  currentDate.getFullYear() + month + date;
            
            filename = `sentrycx_workstation_history_${fullDateFormat}.xlsx`;
            
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
            this.isLoading = false;
        }
    });

    this.workstationHistoryLogForm.reset();
  }
}
