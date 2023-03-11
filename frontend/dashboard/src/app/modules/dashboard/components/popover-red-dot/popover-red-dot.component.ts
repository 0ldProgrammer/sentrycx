import { Component, OnInit, Input } from '@angular/core';
import { IAgent } from '@app/interfaces';

@Component({
    selector: 'popover-red-dot',
    templateUrl: './popover-red-dot.component.html'
})
export class PopoverRedDotComponent implements OnInit {
    @Input('data-agent') agent : IAgent;

    popoverPositionX = 15;
    popoverPositionY = -10;

    private THRESHOLD = {
        download_speed : 10,
        mos : 3.6,
        upload_speed : 5
    };

    private THRESHOLD_FIELDS = ['download_speed', 'mos', 'upload_speed'];

    private THRESHOLD_MSG = {
        download_speed : 'Download Speed is less than 10Mbps',
        mos : 'MOS rating is empty or poor( less than 3.6 )',
        upload_speed : "Upload Speed is less than 5Mbps"
    }

    public hasThreshold : Boolean = false;

    public messages : String[] = [];

    constructor() { }

    ngOnInit(): void { 
        const self = this;
        this.THRESHOLD_FIELDS.forEach( (field, key) => {
            const data = self.agent[field];
            const msg  = self.THRESHOLD_MSG[field]; 
            const threshold = self.THRESHOLD[field];
            
            if( data < threshold && data > 0){
                self.messages.push( msg );
                self. hasThreshold = true;
            }
        });

    }
}
