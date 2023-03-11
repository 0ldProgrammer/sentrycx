import { OnInit, Component } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { TimezoneComponent } from '../timezone/timezone.component';
import { DatePipe } from '@angular/common';
import * as moment from 'moment-timezone';
@Component({
  selector : 'clock',
  templateUrl : './clock.component.html',
  styleUrls   : ['./clock.component.css']
})
export class ClockComponent implements OnInit {

  public runningTimer : number = Date.now();
  public timezone = null;
  public timezoneName = null;

  public selectedTime = {
    date : '-',
    hour : '-',
    min  : '-',
    sec  : '-',
    time : '-',
    mode : '-'
  }

  public updatedTimezone;
  constructor( private dialog  : MatDialog, ){}

  public ngOnInit(){
    const self = this;
    // setInterval( () => {
    //   self.runningTimer = Date.now();
    // }, 1000);

    this.setUserTimezone();
    this.setUserTimezoneName();
  }

  public setUserTimezone(){
    let userTimezone = localStorage.getItem('USER_TIMEZONE');

    if( !userTimezone ){
      userTimezone = new Date().getTimezoneOffset().toString();
      localStorage.setItem('USER_TIMEZONE', userTimezone);
    }

    this.timezone = userTimezone;

  }

  public setUserTimezoneName(){
    
    this.timezoneName = this._getTimezoneName();

    const self = this;
      
    setInterval( () => {
      const currentTimezone = self._getTimezoneName();
      const convertedTime = moment().tz(currentTimezone);

      self.selectedTime.date = convertedTime.format('MM/DD/yyyy z');
      self.selectedTime.hour = convertedTime.format('h');
      self.selectedTime.min  = convertedTime.format('mm');
      self.selectedTime.sec  = convertedTime.format('ss');
      self.selectedTime.mode = convertedTime.format('A');
    }, 1000);
   
  }

  private _getTimezoneName(){
    let timezoneName = localStorage.getItem('USER_TIMEZONE_NAME') as string;

    if( !timezoneName ){
        timezoneName = Intl.DateTimeFormat().resolvedOptions().timeZone;
        localStorage.setItem('USER_TIMEZONE_NAME', timezoneName);
    }

    return timezoneName;
  }


  public showTimezoneModal(){
    const modal = this.dialog.open( TimezoneComponent );
    const self = this;
    modal.componentInstance['currentTimezoneName'] = this.timezoneName;
    modal.componentInstance['onSave'].subscribe( args => {
      console.log("ARGS", args);
      // const moment().tz(currentTimezone);
        localStorage.setItem('USER_TIMEZONE_NAME', args['name'] );
        localStorage.setItem('USER_TIMEZONE', args['offset'] );
        self.timezone = args['offest'];
        self.timezoneName = args['name'];

    });
  }
}
