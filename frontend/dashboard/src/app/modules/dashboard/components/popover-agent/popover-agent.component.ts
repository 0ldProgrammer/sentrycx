import { Component, OnInit, EventEmitter , Input, Output} from '@angular/core';
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
    selector: 'popover-agent',
    templateUrl: './popover-agent.component.html',
    styleUrls: ['./popover-agent.component.css']
})
export class PopoverAgentComponent implements OnInit {
    @Input('data-agent-id') agentID : any;
    @Output() onClick = new EventEmitter; 
    public hasData : Boolean = false;
    @Input() profile : IProfile ;

    public PROFILE_FIELDS : Array<IField>= [
        { id : 'agent_name', name :'Employee Name'},
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

    alignButton = 'align-right';
    popoverPositionX = -30;
    popoverPositionY = -10;
    'event' = 'click';
    autoTicks = false;
    disabled = false;
    invert = false;
    max = 100;
    min = 0;
    showTicks = false;
    step = 1;
    thumbLabel = false;
    value = 0;
    vertical = false;
    
    constructor( public service : WorkdayService ) { }

    ngOnInit(): void { 
    }

    clickHandle(agentID){
        this.onClick.emit( agentID );
    }
}
