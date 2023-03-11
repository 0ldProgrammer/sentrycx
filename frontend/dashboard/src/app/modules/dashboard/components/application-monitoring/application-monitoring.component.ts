import { Component, Input, Output, EventEmitter } from '@angular/core';
import { FormControl, Validators } from '@angular/forms';
import { MatDialogRef } from '@angular/material/dialog';
import * as _ from 'lodash';
import swal from 'sweetalert2';

// Define Interfaces here
interface IMenu {
    id : string,
    name : string,
    ref  : string 
}

interface IApplication {
    id: number,
    name: string,
    description: string,
    hosted: string,
    url: string
}


interface IApplicationStatus {
    application : string,
    mtr : string,
    mtr_ref  :  string,
    ping     :  string,
    ping_ref :  string,
    traceroute     : string,
    traceroute_ref : string,
    type: string,
    worker_id: string,
    disk_drive : string
}

@Component({
    selector: 'application-monitoring',
    templateUrl: 'application-monitoring.component.html'
})
export class ApplicationMonitoringComponent {
    public type = 'AUTO';
    public selectedApp : IApplicationStatus = null;
    public extractApp  = new FormControl('', [ Validators.required ]);

    public menu : Array<IMenu>= [{
        id   : 'mtr',
        ref  : 'mtr_ref',
        name : 'MTR',
    }, {
        id   : 'traceroute',
        ref  : 'traceroute_ref',
        name : 'Traceroute',
    },{
        id   : 'ping',
        ref  : 'traceroute_ref',
        name : 'Ping'
    }];

    @Input() agentName : string;
    @Input() automaticApplicationStatus : Array<IApplicationStatus>;
    @Input() manualApplicationStatus    : Array<IApplicationStatus>;
    @Input() agentApplications : Array<IApplication> = [];
    @Output() onExtract = new EventEmitter;
    
    constructor( public dialogRef: MatDialogRef<ApplicationMonitoringComponent>) {}

    /*
     * Sets the selected application
     */ 
    public setApp( appStatus, index ){
        this.selectedApp = appStatus[index];
    }

    /*
     * Sets the selected type, either auto or manual
     */
    public setType(){
        this.selectedApp = null;
        if( this.type == 'AUTO' ){
            this.type = 'MANUAL';
            return;
        }

        this.type = 'AUTO';
    }

    /*
     * Triggers the extraction of application status
     */ 
    public extractAppStatus(){
        
        swal.fire({
            title: 'Extracting Application Status',
            text: `Currently extracting recent MTR, Traceroute and Ping for the ${this.extractApp.value}. It will reflect in a few minutes.`,
            icon: 'success',
            customClass:{
              confirmButton: "btn btn-info",
            },
            buttonsStyling: false
        });

        this.onExtract.emit( this.extractApp.value );

        this.extractApp.reset();
    }
}