import { OnInit, Component, Input, Output, EventEmitter, ViewChild } from '@angular/core';
import * as _ from 'lodash';
import { MatDialogRef, MatDialog } from '@angular/material/dialog';
import {MatAccordion} from '@angular/material/expansion';
import { NotificationService, AuthService, WorkstationService } from '@app/services';
import { WebMTRComponent } from '@modules/dashboard/components';
import { IPaginate } from '@app/interfaces';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { environment } from '@env/environment';
import swal from 'sweetalert2';

@Component({
    selector: 'new-workstation-profile',
    templateUrl: './new-workstation-profile.component.html',
    styleUrls: ['./new-workstation-profile.component.css']
})
export class NewWorkstationProfileComponent implements OnInit {
    @ViewChild(MatAccordion) accordion: MatAccordion;
    @Input() agent : String;
    @Input() singleInfo : Boolean = false;
    @Input() hardwareInfo : any; 
    @Input() lezap        : any;
    @Input() agentName : String;
    @Input() progress  = 0;
    @Input() paginate  : IPaginate;
    @Input() isLoading : Boolean = true;
    @Input() eventLogs : Array<String>;
    @Input() pacDetails : Array<any> = [];
    @Input() mediaDevices : any;
    @Input() remoteSessionLogs : Array<String>;
    @Input() speedtest : any;
    @Output() onMediaStatsRequest = new EventEmitter;
    @Output() onRequest = new EventEmitter;
    @Output() onUpdate  = new EventEmitter;
    @Output() onDestroy = new EventEmitter;
    @Output() onEventExtract = new EventEmitter;
    @Output() onAgentLock = new EventEmitter;
    @Output() onAgentWipeout   = new EventEmitter;

    public eventLogForm = new FormGroup({
       startDate : new FormControl('', Validators.required),
       endDate : new FormControl('', Validators.required),
       type    : new FormControl('application'),
       keyword : new FormControl('')
    });

    public subPage : String = 'Workstation Profile';
    public subTab;
    public appsToCheck : Array<string> = ['FireEye', 'Symantec'];

    constructor(
      public dialogRef: MatDialogRef<NewWorkstationProfileComponent>,
      public dialog: MatDialog,
      private notification : NotificationService,
      public auth : AuthService,
      public workstationService :WorkstationService
    ){}
  
    ngOnInit(){
      this.agent = this.agent;
    }

    /*
    * Check the page if its the current page
    */ 
    public isSelected( pageName){
        return this.subPage === pageName;
    }

    public setPage(pageName) {
      this.subPage = pageName;
		this.subTab = '';
		
		return this.isSelected(pageName);
    }

    public setTabs(pageName){
      this.subTab = pageName;
		this.subPage = '';
		
		return this.isSelectedTab(pageName);
    }

    public isSelectedTab( pageName){
        return this.subTab === pageName;
    }
     /*
      * Checks if the app is installed
      */ 
     public isInstalled( app ){
      if( !this.lezap ) return false;

      const installedApps = this.lezap['installed_apps'] as String;

      return  installedApps.includes( app );
  }

  /*
   * Scan all the applications if the
   * necessary apps are installed
   */
  public haveRequiredApps(){
    if( !this.lezap ) return false;
    let hasNeededApps = false ;
    const installedApps = this.lezap['installed_apps'] as String;
    
    this.appsToCheck.forEach( ( app ) => {
      if( !installedApps.includes(app) )
        return;
      hasNeededApps = true;
    });
    return hasNeededApps;
 }

  /*
   * Creates an array based on range
   */
  public range(n: number): any[] {
    return Array(n);
  }

  /*
   * Triggers the changing of 
   * workstation page
   */
  public updateData( pageNo ){
    this.onUpdate.emit( pageNo );
  }

  /*
  * Triggers the send request 
  * button to extract updated workstation
  */
  public sendRequest(){
    const params = {
        selected_ip   : this.hardwareInfo.selected_ip,
        selected_host : this.hardwareInfo.selected_host,
        worker_id     : this.hardwareInfo.worker_id
    };
    this.onRequest.emit( params );
  }

  public sendMediaCheckRequest(){
    this.notification.alert('Media Device Check', 'Device Check Notification has been sent to the agent.')
    this.onMediaStatsRequest.emit( this.hardwareInfo.worker_id )
  }

  /*
   * Triggers the extract event logs
   * 
   */
  public extractLog(){
    if( this.eventLogForm.invalid )
      return;

    let formData = this.eventLogForm.value;
    formData['date_start'] = formData['startDate'].toISOString().substr(0,10);
    formData['date_end']   = formData['endDate'].toISOString().substr(0,10)
    formData['session_id'] = this.hardwareInfo.session_id;
    
    this.onEventExtract.emit( formData );

    this.eventLogForm.reset();
  }

   /*
   * Get the log details based on name
   * 
   */
  public getLogDetails( logString, logName ){
    const logDetails = _.split( logString, '_');
    const nameRef = {
       timestamp  : 0,
       type       : 1,
       startDate  : 2,
       endDate    : 3,
       keyword    : 4
     };

     let logValue = logDetails[ nameRef[logName ]];

   return logValue;
  }

  /*
  * Download Selected Log
  */
  public downloadLog( path ){
    window.location.href = environment.apiURL + `/workstation/logs/${this.hardwareInfo.worker_id}/download?path=${path}`;
  }
  
    /*
    * Triggers the lock screen
    */
  public lockAgent(){
      const sessionID = this.hardwareInfo['session_id']
      if(this.auth.isAllowed('event-approvals:page')) {   
      swal.fire({
          title:"Confirm Lock PC",
          html:`<h6>Please Enter the reason for this Lock PC Action</h6><textarea id="remarks" class="form-control"></textarea>
          `,
          confirmButtonText: 'Yes, Lock it!',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          focusConfirm: false,
          preConfirm: () => {
            const remarks = swal.getPopup().querySelector('#remarks')['value']
            if(!remarks){
              swal.showValidationMessage(`Please enter a remark`)
            }
          return {remarks: remarks}
          }
        }).then((result) => {
          if (result.isConfirmed) {
            const user = this.auth.getUser()
                const worker_id = this.hardwareInfo['worker_id']
                const details = {
                  requested_by : user.firstname,
                  event : 'PC Lock',
                  agent_name : this.hardwareInfo['agent_name'],
                  execution_type : 1,
                  remarks : result.value.remarks
                }
      
                this.workstationService.lockAgentWorkstation(worker_id, details).then((data) => {
                  
                    swal.fire({
                      title: 'Lock Workstation',
                      text: 'Workstation Lock Successfully Executed',
                      icon: 'success',
                      customClass:{
                        confirmButton: "btn btn-info",
                      },
                      buttonsStyling: false
                    });
      
                  console.log(data)
              })    
            }
        })
      }else{
        swal.fire({
          title: 'Warning!',
          text: 'You are not authorize to do this action',
          icon: 'error',
          customClass:{
            confirmButton: "btn btn-info",
          },
          buttonsStyling: false
        });
  
      }
  }

  /* 
  * Notify manager for wipeout approval
  */
  public notifyManager(){
    const excludeStations = ['DESKTOP-S73AVUV','CNXc'];

    if(this.auth.isAllowed('event-approvals:page')) {   
      if( excludeStations.indexOf( this.hardwareInfo.station_number ) !== -1 ){
        swal.fire({
          title: 'Wipeout Request',
          text: 'Unable to wipeout workstation. Workstation is being used by development team.',
          icon: 'error',
          customClass:{
            confirmButton: "btn btn-info",
          },
          buttonsStyling: false
        });
        return;
      }

      swal.fire({
        title:"Confirm Wipeout",
        html:`<h6>Please Enter the reason for this Wipeout Action</h6><textarea type="text" id="remarks" class="form-control"></textarea>
        `,
        confirmButtonText: 'Yes, Wipe It Out!',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        focusConfirm: false,
        preConfirm: () => {
          const pass = swal.getPopup().querySelector('#remarks')['value']
          if(!pass){
            swal.showValidationMessage(`Please enter a remark`)
          }
        return {pass: pass}
        }
      }).then((result) => {
        if (result.isConfirmed) {
          const user = this.auth.getUser()
              const worker_id = this.hardwareInfo['worker_id']
              const details = {
                requested_by : user.firstname,
                event : 'PC Wipeout',
                agent_name : this.hardwareInfo['agent_name'],
                execution_type : 1,
                remarks : result.value.pass
              }
    
              this.workstationService.lockAgentWorkstation(worker_id, details).then((data) => {
                
                  swal.fire({
                    title: 'Wipeout Workstation',
                    text: 'PC Wipeout Successfully Executed',
                    icon: 'success',
                    customClass:{
                      confirmButton: "btn btn-info",
                    },
                    buttonsStyling: false
                  });
    
                console.log(data)
            })

            //console.log(user)
            //console.log(this.hardwareInfo)
          
          }
      })
    }else{
      swal.fire({
        title: 'Warning!',
        text: 'You are not authorize to do this action',
        icon: 'error',
        customClass:{
          confirmButton: "btn btn-info",
        },
        buttonsStyling: false
      });

    }
}

 public loadWebMTR(agentId) {
    const self = this;

    const modal = self.dialog.open(WebMTRComponent, { panelClass: 'custom-full-dialog'});

    modal.componentInstance['agentId'] = agentId;
  }
  
}
