import { Component, OnInit, Input } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { AccountsService , AuthService, NotificationService} from '@app/services/index';
import { FormControl } from '@angular/forms';
import swal from 'sweetalert2';

@Component({
    selector: 'hostfile',
    templateUrl: './hostfile.component.html',
    styleUrls : ['./hostfile.component.css']
})
export class HostfileComponent implements OnInit {
    @Input('data-account') account : String = '';
    public editMode : Boolean = false;
    public hostfileText = new FormControl(null);
    public isLoading : Boolean = true;

    constructor( 
        public dialogRef: MatDialogRef<HostfileComponent>,
        public service : AccountsService,
        public notification : NotificationService,
        private auth : AuthService
    ) {}

    ngOnInit(): void { 
        const self = this;
        this.service.getHostfile( this.account ).subscribe( data => {
            self.hostfileText.setValue( data );
            self.isLoading = false;
        });
    }

   /*
    * Saves and update the hostfile
    *
    */
    public update( deploy ){

        swal.fire({
            title:"Confirm Update Hostfile",
            text: 'Are you Sure, You want to save this Hostfile?',
            confirmButtonText: 'Yes, Save it!',
            showCancelButton: true,
            cancelButtonColor: '#d33',
            focusConfirm: false
          }).then((result) => {
              const user = this.auth.getUser()
              const details = {
                requested_by : user.firstname,
                event : 'Update Hostfile',
                agent_name : this.account,
                hostfileText : this.hostfileText.value,
                deploy : deploy
              }

              this.editMode = false;
              this.service.updateHostfile( this.account, details ).then((data) => {
          
                swal.fire({
                  title: 'Update Hostfile',
                  text: 'Hostfile Successfully Updated',
                  icon: 'success',
                  customClass:{
                    confirmButton: "btn btn-info",
                  },
                  buttonsStyling: false
                });
            })

            // if( deploy )
            //   this.notification.alert( 'Hostfile updated' ,`Centralized hostfile for ${this.account} has been updated! Workstations under this account will be updated.`);
            // else 
            //     this.notification.alert( 'Hostfile saved' ,`Centralized hostfile for ${this.account} has been saved! Workstations under this account won't be updated.`);
  
            
      
          })
          console.log(this.account)
       
    }
}
