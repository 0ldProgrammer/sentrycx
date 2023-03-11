import { Component, OnInit, Input } from '@angular/core';
import { HistoricalService, WorkstationService, TriggerService ,NotificationService } from '@app/services';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import swal from 'sweetalert2';
interface Field {
    id : string;
    name : string;
}

interface TraceRecord {
    traceroute: string;
    traceroute_ref: string;
}


interface TraceApplications {
    url: string;
    id: number;
}

interface TraceTimestamps {
    created_at: string;
    id: number;
}

@Component({
    selector: 'trace',
    templateUrl: './trace.component.html'
})
export class TraceComponent implements OnInit {
    @Input('data-agent') agent : String;
    @Input('data-worker-id') workerID : String;
    @Input('data-readonly') readonly : Boolean = false;

    public isLoading : Boolean = true;

    public initialTimestamp : String;
    public initialApplication : String;
    public gridApplication : String;

    public records : Array<TraceRecord> = [];

    public applications : Array<TraceApplications> = [];
    public timestamps : Array<TraceTimestamps> = [];
    public selectedTimestampId : number;
    public selectedTimestamp : String;
    public selectedApplication : String;

    public traceLogForm = new FormGroup({
        startDate : new FormControl('', Validators.required),
        endDate : new FormControl('', Validators.required)
     });

    constructor( 
        private service : HistoricalService,
        private triggers : TriggerService,
        private notification : NotificationService,
        private workstationService : WorkstationService
    ) { }

    ngOnInit(): void { 
        this.listOfApplications();
        this.listOfTimestamp();
    }

    public listOfApplications(){
        const self = this;
        this.service.getPingApplications(this.workerID).subscribe( response => {
            self.initialApplication = response[0];
            self.setApplication(self.initialApplication);
            self.applications = response as Array<TraceApplications>;
            self.isLoading = false;
        });
       }
    
        /*
         * Change the application selected
         */
        public setApplication( setApplication ) {
            this.isLoading = true;
            this.selectedApplication =  setApplication.url;
            this.listOfTimestamp();
            this.isLoading = false;
        }
    
        public listOfTimestamp(){
            const self = this;
            self.isLoading = true;
            this.service.getTimestamps(this.workerID, this.selectedApplication).subscribe( response => {
                self.initialTimestamp = response[0];
                self.setTimestamp(self.initialTimestamp);
                self.timestamps = response as Array<TraceTimestamps>;
                self.isLoading = false;
            });
        }
    
        /*
         * Change the timestamp selected
         */
        public setTimestamp( setTimestamp ) {
            this.isLoading = true;
            if (setTimestamp) {
                this.selectedTimestampId = setTimestamp.id;
                this.selectedTimestamp = setTimestamp.created_at;
            }
            this.isLoading = false;
            this.loadTrace();
        }

        public loadTrace() {
            this.gridApplication = this.selectedApplication;
            this._loadTraceData();
        }
    
        public _loadTraceData(){
            const self = this;
            this.workstationService.getApplicationStatus( this.workerID, this.selectedTimestampId ).subscribe( response => {
                const data = response['data'];
                self.records = data[0];
                self.isLoading = false;
            });
        }

    /*
   * Generate Trace logs report
   * 
   */
  public extractLog(){
    
    let userReportTimezone = localStorage.getItem('USER_TIMEZONE_NAME');

    if( this.traceLogForm.invalid )
      return;

    let formData = this.traceLogForm.value;
    
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

    this.service.traceDownloadReport(this.workerID, startDateTime, endDateTime, userReportTimezone ).subscribe( (data) => {
        let filename = null;
        let currentDate = new Date();
        let month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
        let date = ("0" + currentDate.getDate()).slice(-2);
        let fullDateFormat =  currentDate.getFullYear() + month + date;
        
        filename = `sentrycx_traceroute_${fullDateFormat}.xlsx`;
        
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

    this.traceLogForm.reset();
  }

}
