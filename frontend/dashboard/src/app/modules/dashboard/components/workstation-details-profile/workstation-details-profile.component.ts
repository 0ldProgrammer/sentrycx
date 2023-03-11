import { Component, OnInit, Input } from '@angular/core';
import { TimezonePipe } from '@app/pipes';
interface IFields {
    name : string,
    value : string
}

@Component({
    selector: 'workstation-details-profile',
    templateUrl: 'workstation-details-profile.component.html'
})

export class WorkstationDetailsProfileComponent implements OnInit {
    @Input('data-agent-name') agentName : String;
    @Input('data-profile') hardwareInfo : any;
    public profileFields : Array<IFields> = [];
    public version : String;

    constructor() { }

    ngOnInit() { 
        const timezone = new TimezonePipe;
        const convertedTime = timezone.transform( this.hardwareInfo['updated_at'] );
        this.profileFields = [
            {name:"Agent Name", value: this.agentName },
            {name:"Date Extracted", value: `${convertedTime} (v:${this.hardwareInfo['desktop_app_version']}) `},
            {name:"IP Address", value: this.hardwareInfo['host_ip_address'] },
            {name:"Workstation", value: this.hardwareInfo['station_number'] },
            {name:"Subnet", value: this.hardwareInfo['subnet'] },
            {name:"Gateway", value : this.hardwareInfo['gateway'] },
            {name:"DNS", value: `${this.hardwareInfo['DNS_1']}/${this.hardwareInfo['DNS_2']}`  },
            // {name:"DNS 1", value: this.hardwareInfo['DNS_1'] },
            // {name:"DNS 2", value: this.hardwareInfo['DNS_2'] },
            {name:"VLAN", value: this.hardwareInfo['VLAN'] },
            {name:"Download Speed", value: this.hardwareInfo['download_speed'] ? this.hardwareInfo['download_speed'] + " Mbps" : '-'},
            {name:"Upload Speed", value: this.hardwareInfo['upload_speed'] ? this.hardwareInfo['upload_speed'] + "Mbps" : '-'},
            {name:"ISP", value: this.hardwareInfo['ISP'] }
        ]
    }

}