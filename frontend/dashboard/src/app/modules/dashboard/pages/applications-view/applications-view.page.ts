import { Component, OnInit } from '@angular/core';
import { WorkstationService } from '@app/services';
import {FormControl} from '@angular/forms';

interface IField {
    field : string,
    name : string,
    class : string 
}

@Component({
    selector: 'applications-view',
    templateUrl: './applications-view.page.html'
})
export class ApplicationsViewPage implements OnInit {
    public accountStats = [];
    public locationStats;
    public totalStats;
    public selectedAccount = null;
    public header;
    public applicationsType = 'Required';
    public isLoading : Boolean = true;
    public note = 'Note : Display count of non-installed Required Applications for the last 24 hours';

    public reportType = [
        { value : 'excel', name: 'Export to Excel'}
      ]

    public search : any;

    filter = new FormControl('');

    constructor( 
        private service : WorkstationService
    ) { }

    ngOnInit(): void {
        this.loadData();
        this.filter.valueChanges.subscribe(val => {
            this.searchValue(val)
        });
    }

    public selectAccount( account ){
        if( this.selectedAccount == account )
            account = null;
        this.selectedAccount = account;
    }

    public searchValue(val)
    {
        const self = this;
        this.service.searchApplicationAccounts(this.applicationsType, val).subscribe(response => {
            self.isLoading = false;
            self.accountStats  = response['accounts'];
            self.locationStats = response['location'];
            self.totalStats    = response['total'];  
        });
        this.search = val;
    }

    public loadData(){
        const self = this;
        self.isLoading = true;
        this.service.getAgentsApplications(this.applicationsType).subscribe( response => {
            self.isLoading = false;
            this.header = Object.keys(response['accounts'][0]);
            self.accountStats  = response['accounts'];
            self.locationStats = response['location'];
            self.totalStats    = response['total'];
        });

    }

    // select type of report
    public setReport( type ){
        this.service.downloadReportFromApplicationsView( type, this.applicationsType ).subscribe( (data) => {
            let filename = null;
            let currentDate = new Date();
            let month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
            let date = ("0" + currentDate.getDate()).slice(-2);
            let fullDateFormat =  currentDate.getFullYear() + month + date;
            
            if (type === 'excel') {
                filename = `sentrycx_${this.applicationsType.toLowerCase()}_applications_${fullDateFormat}.xlsx`;
            } else {
                filename = `sentrycx_applications_${fullDateFormat}.pdf`;
            }
            
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
    }

    public search_value = '';

    public applicationsTypeSelected(event) {
        this.applicationsType = event['index'] == 0 ? 'Required' : 'Restricted';
        this.loadData();
        this.note = event['index'] == 0 ? 'Note : Display count of non-installed Required Applications for the last 24 hours' : 'Note : Display count of installed Restricted Applications for the last 24 hours';
        this.search_value = '';
    }
}
