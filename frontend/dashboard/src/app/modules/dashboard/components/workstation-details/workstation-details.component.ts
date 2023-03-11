import { 
    Component,
    OnDestroy,
    Input,
    Output,
    EventEmitter,
 } from '@angular/core';
import { MatDialogRef, MatDialog } from '@angular/material/dialog';
import { WebMTRComponent } from '@modules/dashboard/components';
import { IPaginate } from '@app/interfaces';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import * as _ from 'lodash';
import { environment } from '@env/environment';
import swal from 'sweetalert2';

import { NotificationService, AuthService, WorkstationService } from '@app/services';


 

@Component({
    selector: 'workstation-details',
    templateUrl: 'workstation-details.component.html',
    styleUrls : ['./workstation-details.component.css']
})

/*
 * TODOs : 
 *    - Convert every Workstation Page into component as this source file is getting expanded a lot
 *      (ex <senventory-component> for Senventory page, <rpd-logs> for RDP Logs page )
 * .    This is to breakdown the codes into pieces
 * 
 * */
export class WorkstationDetailsComponent implements OnDestroy {
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

    public SECTION_MENU = [
      'Workstation Profile',
      'Workstation History',
      'Ping',
      'Throughput',
      'Telnet',
      'Traceroute',
      'Host File',
      'MTR',
      'Senventory',
      'Proxy Auto-Config (PAC)',
      'Event Logs',
      'ZOHO Logs',
      'Media Device',
      'Mean Opinion Score(MOS)',
      'Speedtest',
      'SecureCX Monitoring'
    ]

    uninstalledApps : Array<string> = [];

    public appsToCheck : Array<string> = ['FireEye', 'Symantec'];

    public lezapIndex = [
        'processor',
        'gpu',
        'disk_drive',
        'os',
        'sound_card',
        'network_interface',
        'ram',
        'memory',
        'printer',
        'monitor',
        'camera',
        'mouse',
        'keyboard',
        'mother_board',
        'installed_apps',
    ];

    public lezapList = {
        ram : 'RAM',
        memory : 'Memory',
        processor :  'Processor',
        gpu : 'GPU',
        disk_drive : 'Hard Drive',
        os : 'Operating System',
        sound_card: 'Sound',
        network_interface  : 'Network',
        printer  : 'Printer',
        monitor  : 'Monitor',
        camera   : 'Camera',
        mouse    : 'Mouse',
        keyboard :'Keyboard',
        installed_apps : 'Installed Applications',
        mother_board : 'Motherboard'
    }

    public subPage : String = 'Workstation Profile';

    public readOnly : Boolean = this.auth.isAllowed('users:read-only');

    public deviceRef = [
      { id : 'audio', name : 'Speaker', icon : 'headset' },
      { id : 'mic',   name : 'Microphone', icon : 'mic' },
      { id : 'video', name : 'Camera', icon : 'camera_alt' }
    ]
    constructor( 
      public dialogRef: MatDialogRef<WorkstationDetailsComponent>,
      public dialog: MatDialog,
      private notification : NotificationService,
      public auth : AuthService,
      public workstationService :WorkstationService
      
    ) { }


    ngOnDestroy(){
        this.onDestroy.emit();
    }


    /*
     * Change the page
     */
    public setPage( pageName ) {
        this.subPage = pageName;
    }

    /*
     * Check the page if its the current page
     */ 
    public isSelected( pageName){
        return this.subPage === pageName;
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
    console.log('hasNeededApps', hasNeededApps );
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
     if(this.auth.isAllowed('admin:lock-wipeout')) {   
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

      // swal.fire({
      //   title:"Confirm Lock Workstation",
      //   html:`<input type="text" id="username" class="form-control" value="administrator" disabled >
      //   <input type="password" id="password" class="form-control" placeholder="Password">`,
      //   confirmButtonText: 'Submit Request',
      //   focusConfirm: false,
      //   preConfirm: () => {
      //     const pass = swal.getPopup().querySelector('#password')['value']

      //     if(!pass){
      //       swal.showValidationMessage(`Please enter a password`)
      //     }
      //   return {pass: pass}
      //   }
      // }).then((result) => {
      //     const user = this.auth.getUser()
      //     const worker_id = this.hardwareInfo['worker_id']
      //     const details = {
      //        requested_by : user.firstname,
      //        event : 'PC Lock',
      //        agent_name : this.hardwareInfo['agent_name'],
      //        params : {password: result.value.pass}
      //     }

      //     this.workstationService.lockAgentWorkstation(worker_id, details).then((data) => {
            
      //         swal.fire({
      //           title: 'Lock Workstation Request',
      //           text: 'Request Successfully Submitted',
      //           icon: 'success',
      //           customClass:{
      //             confirmButton: "btn btn-info",
      //           },
      //           buttonsStyling: false
      //         });

      //         console.log(data)
      //     })

      //     //console.log(user)
      //     //console.log(this.hardwareInfo)
      // })
   }

   /* 
    * Notify manager for wipeout approval
    */
   public notifyManager(){
    const excludeStations = ['DESKTOP-S73AVUV','CNXc'];

    if(this.auth.isAllowed('admin:lock-wipeout')) {   
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
    // swal.fire({
    //   title:"Confirm Wipeout",
    //   html:`<input type="text" id="username" class="form-control" value="administrator" disabled >
    //   <input type="password" id="wipePass" class="form-control" placeholder="Password">`,
    //   confirmButtonText: 'Submit Request',
    //   focusConfirm: false,
    //   preConfirm: () => {
    //     const pass = swal.getPopup().querySelector('#wipePass')['value']
    //     if(!pass){
    //       swal.showValidationMessage(`Please enter a password`)
    //     }
    //   return {pass: pass}
    //   }
    // }).then((result) => {
    //     const user = this.auth.getUser()
    //     const worker_id = this.hardwareInfo['worker_id']
    //     const details = {
    //       requested_by : user.firstname,
    //       event : 'PC Wipeout',
    //       agent_name : this.hardwareInfo['agent_name'],
    //       params : {password: result.value.pass}
    //     }

    //     this.workstationService.lockAgentWorkstation(worker_id, details).then((data) => {
          
    //         swal.fire({
    //           title: 'PC Wipe Out Request',
    //           text: 'Request Successfully Submitted',
    //           icon: 'success',
    //           customClass:{
    //             confirmButton: "btn btn-info",
    //           },
    //           buttonsStyling: false
    //         });

    //         console.log(data)
    //     })
    // })
   // this.onAgentWipeout.emit( this.hardwareInfo.worker_id );

    // swal.fire({
    //   title: 'Wipeout Request',
    //   text: 'This request  is sent to your manager for validation',
    //   icon: 'success',
    //   customClass:{
    //     confirmButton: "btn btn-info",
    //   },
    //   buttonsStyling: false
    // });

    }

    public loadWebMTR(agentId) {
      const self = this;
  
      const modal = self.dialog.open(WebMTRComponent, { panelClass: 'custom-full-dialog'});
  
      modal.componentInstance['agentId'] = agentId;
    }


}
