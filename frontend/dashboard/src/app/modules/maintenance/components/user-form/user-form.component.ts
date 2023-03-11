import { OnChanges, Component, Input, Output, EventEmitter,ElementRef, ViewChild } from '@angular/core';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import {Observable} from 'rxjs';
import {map, startWith} from 'rxjs/operators';
import { find } from 'lodash';

@Component({
  selector: 'user-form-component',
  templateUrl: './user-form.component.html',
  styleUrls: ['./user-form.component.css']
})
export class UserFormComponent implements OnChanges {
    public userForm = new FormGroup({
        id: new FormControl(''),
        email: new FormControl('', [Validators.required, Validators.email]),
        firstname: new FormControl('', Validators.required),
        username: new FormControl('', Validators.required),
        location: new FormControl(''),
        account_access: new FormControl(''),
        access_view: new FormControl(''),
        scope_access : new FormControl('')
    });

    public selectedAccess : any;

    public ACCESS_SCOPES = [
      { id : 'users:scope', name : 'User Access' },
      { id : 'users:read-only', name : 'User Access (Read Only)' },
      { id : 'admin:connected-itss', name : 'Connected ITSS' },
      { id : 'admin:lock-wipeout', name : 'PC Lock / Wipeout' },
      { id : 'admin:audit-logs', name : 'Audit Logs' },
      { id : 'admin:unresolve-issues', name : 'Unresolve Issues' },
      { id : 'admin:idle-agents', name : 'Idle Agents' },
      { id : 'admin:unlisted', name : 'Unlisted' },
      { id : 'audit-logs:page', name: 'Audit Logs'},
      { id : 'users:accounts', name : 'Accounts Management' },
      { id : 'users:codes', name : 'Codes Assignment' },
      { id : 'users:vlan', name : 'VLAN Mapping' },
      { id : 'users:subnet', name : 'Subnet Mapping' },
      { id : 'admin:web-cmd', name : 'Web CMD / PS'},
      { id : 'users:deployment', name : 'Deployment Management' },
      { id : 'admin:geo-mapping', name : 'Geo Mapping'},
      { id : 'users:applications-list', name : 'Applications List'},
      { id : 'users:securecx', name : 'SecureCX'},
      { id : 'admin:vpn-approval', name : 'VPN Approval'},
      { id : 'users:applications-urls', name : 'Applications URLs'},
      { id : 'admin:software-update', name : 'Software Update'},
      { id : 'admin:mail-notification', name : 'Mail Notification'},
      { id : 'admin:time-frame', name: 'Time Frame'},
      { id : 'users:summary', name : 'Summary'}
    ];

    public ADMIN_Access = [
      'admin',
      'admin:connected-itss', 
      'admin:lock-wipeout',
      'admin:audit-logs',
      'admin:unresolve-issues',
      'admin:idle-agents',
      'admin:unlisted',
      'audit-logs:page',
      'admin:web-cmd',
      'admin:geo-mapping',
      'admin:vpn-approval',
      'admin:software-update',
      'users:application-urls',
      'users:securecx',
      'users:applications-list',
      'admin:mail-notification',
      'admin:time-frame',
      'users:summary'
    ];

    public USER_READONLY = ['users:read-only'];

    
    public siteLocations : string[] = [];
    @Input('data') data: any;

    @Input('data-sites') sites: Array<string>;

    @Input('data-accounts') accounts: Array<string>;

    @Output() onSave: EventEmitter<string> = new EventEmitter();

    ngOnChanges() {
      if(this.data != undefined){
        
        this.userForm.patchValue(this.data);
        console.log(this.data)
      }
    }

    /*
     * Assignes the value of location 
     * based on the what autocomplete provided
     */
    onLocationChange( location ){
      this.userForm.controls['location'].setValue( location );
    }

    /*
     * Assignes the value of account 
     * based on the what autocomplete provided
     */
    onAccountChange( account_access ){
      this.userForm.controls['account_access'].setValue( account_access );
    }

    /*
     * Assigns the value of the scope access
     * based on the what autocomplate provided
     */ 
    onScopeAccessChange( scopeAccess ){
      
      if(scopeAccess.value!='admin')
      {
        this.userForm.controls['scope_access'].setValue( scopeAccess.value );
      }else{
        this.selectedAccess = this.ADMIN_Access;
        this.userForm.controls['scope_access'].setValue( this.selectedAccess );
      }
    }
    /*
    /*
     * Checks if the individual fields are valid
     */
    isInvalid(fieldName){
      const fieldControl = this.userForm.controls[fieldName];

      return (fieldControl.touched && fieldControl.invalid);
    }

    /*
     * Execues the bind submit function
     */
    submit(form){
      if (this.userForm.invalid)
        return;
      const self = this;
      //  alert(this.userForm.controls['scope_access'].value)
      // var scopeData = [];
      // if( form.value.scope_access ){
      //   form.value.scope_access.forEach( (scopeName) => {
      //     scopeData.push( self.accessList[scopeName] );
      //   });
  
      //   this.userForm.controls['scope_access'].setValue( scopeData );
      // }
      
      this.onSave.emit(form.value);

      if (!this.userForm.controls['id'].value)
        this.userForm.reset();
    }

  /*
   * Handles reset of the form
   */
  public clear() {
    this.userForm.reset();
  }
}
