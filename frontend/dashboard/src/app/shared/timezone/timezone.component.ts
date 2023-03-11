import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { FormControl, FormGroup, Validators} from '@angular/forms';
import { getTimeZones, timeZonesNames } from "@vvo/tzdb";
import { NotificationService } from '@app/services';
import * as moment from 'moment-timezone';
import * as _ from 'lodash';

@Component({
    selector: 'timezone',
    templateUrl: './timezone.component.html'
})
export class TimezoneComponent implements OnInit {
    public timezoneList;

    @Input() currentTimezoneName : string;
    @Output() onSave = new EventEmitter;

    public timezoneForm = new FormGroup({
        timezone : new FormControl('', Validators.required )
    });
    constructor( 
        private dialog :  MatDialogRef<TimezoneComponent>,
        private notification : NotificationService
    ) { }

    ngOnInit(): void {
        this.timezoneList = timeZonesNames;
        this.timezoneForm.controls['timezone'].setValue( this.currentTimezoneName );
     }

    public save(){
        const selectedTimezone = this.timezoneForm.controls['timezone'].value ;
        
        const args = {
            offset : this.getTimezoneOffset( selectedTimezone ),
            name : selectedTimezone
        };

        const updatedTime = moment().tz( selectedTimezone );

        console.log("CURRENT", moment().tz( selectedTimezone ).toDate() );
        console.log("TIME!", new Date(updatedTime.format()).getTimezoneOffset() );
        
        this.close();
        this.notification.success(`Timezone has been updated to ${selectedTimezone}`);
        this.onSave.emit( args );
    }

    public close(){
        this.dialog.close();
    }

    public getTimezoneOffset(timeZone) {
        const timezoneDetails = _.find(getTimeZones(), { name : timeZone });
		return timezoneDetails.currentTimeOffsetInMinutes;
      }
}
