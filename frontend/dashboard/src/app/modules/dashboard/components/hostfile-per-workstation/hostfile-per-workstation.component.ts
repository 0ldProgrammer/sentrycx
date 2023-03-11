import { Component, OnInit, Input } from '@angular/core';
import {  WorkstationService, NotificationService} from '@app/services/index';
import { FormControl } from '@angular/forms';
import swal from 'sweetalert2';

@Component({
    selector: 'hostfile-per-workstation',
    templateUrl: './hostfile-per-workstation.component.html',
    styleUrls : ['./hostfile-per-workstation.component.css']
})
export class HostfilePerWorkstationComponent implements OnInit {
    @Input('data-worker-id') workerID : String = '';
    @Input('data-hostfile') hostfile : String = '';
    public editMode : Boolean = false;
    public hostfileText = new FormControl(null);


    constructor( 
        public service : WorkstationService,
        public notification : NotificationService
    ) {}

    ngOnInit(): void { 
        this.hostfileText.setValue( this.hostfile );
    }

   /*
    * Saves and update the hostfile
    *
    */
    public update(  ){
        // this.notification.alert( 'Hostfile updated' ,`Hostfile has been updated! Workstation will be updated.`);
        // this.service.updateHostfile( this.workerID, this.hostfileText.value );
        
    swal.fire({
        title:"Confirm Update Hostfile",
        text: 'Are you Sure, You want to save this Hostfile?',
        confirmButtonText: 'Yes, Save it!',
        showCancelButton: true,
        cancelButtonColor: '#d33',
        focusConfirm: false
      }).then((result) => {
            const details = {
                params : {password: result.value.pass},
                hostfile : this.hostfileText.value,
            }
        
            this.service.updateHostfile( this.workerID, details );


            swal.fire({
                title: 'Update Hostfile',
                text: 'Hostfile Successfully Updated',
                icon: 'success',
                customClass:{
                  confirmButton: "btn btn-info",
                },
                buttonsStyling: false
              });

            this.editMode = false;
      })
            
    }
}
