import { Component, OnInit, Input } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { WorkdayService } from '@app/services';

interface IProfile {
    country: string,
    email: string,
    employee_number?: string,
    firstname: string,
    job_profile: string,
    lastname: string,
    lob?: string,
    location_name?: string,
    msa_client?: string,
    programme_msa?: string,
    supervisor_email_id?: string,
    supervisor_full_name? : string,
}

interface IField {
    id : string
    name : string,
}

@Component({
    selector: 'agent-profile',
    templateUrl: './agent-profile.component.html'
})
export class AgentProfileComponent implements OnInit {
    @Input() workerID : String;
    public hasData : Boolean = false;
    public profile : IProfile ;

    public PROFILE_FIELDS : Array<IField>= [
        { id : 'employee_number', name :'WorkerID'},
        { id : 'email', name : 'Email'},
        { id : 'supervisor_email_id', name :'Manager Email' }, 
        { id : 'supervisor_full_name', name :'Manager Name' },
        { id : 'country', name : 'Country' },
        { id : 'job_profile', name : 'Position' },
        { id : 'location_name', name :'Site Location' },
        { id : 'msa_client', name :'MSA Client' },
        { id : 'programme_msa', name :'PROGRAMME MSA' }
    ];

    constructor( 
        public dialogRef: MatDialogRef<AgentProfileComponent>,
        public service : WorkdayService
      ) { }

    ngOnInit(): void { 
        const self = this;
        this.service.getProfile( this.workerID ).subscribe( response => {
            self.profile = response['profile'];
            self.hasData = response['status'] == 'OK';
        });
    }
}
