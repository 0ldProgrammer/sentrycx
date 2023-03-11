import { Component, OnInit,ViewChild } from '@angular/core';
import { AccountsService, NotificationService } from '@app/services/index';
import { IPaginate } from '@app/interfaces/index';
import { PaginateFactory } from '@app/factory/index';
import { HostfileComponent } from '@modules/dashboard/components';
import { MatDialog } from '@angular/material/dialog';
import { AccountFormComponent } from './../../components/account-form/account-form.component';
import { FormControl, FormGroup, Validators } from '@angular/forms';

interface Account {
    id:  number,
    check_device_url: string,
    check_hostfile_url: string,
    is_active: Boolean,
    name: string,
    need_device_check: Boolean,
    need_hostfile_url: Boolean,
    has_securecx: Boolean,
    check_sites_devices: Boolean
}

@Component({
    selector: 'accounts-page',
    templateUrl: './accounts.page.html',
    styleUrls : ['./accounts.page.css']
})
export class AccountsPage implements OnInit {
    public pageNo = 1;
    public paginate : IPaginate = PaginateFactory.init() ;
    public accounts : Array<Account> = [];
    public selectedAccount : Account;
    public editMode : Boolean = false;


    filter = new FormControl('');
    mediaCheck = new FormControl('');
    isActive = new FormControl('');
    secureCX = new FormControl('');
    active = "";
    medCheck = "";
    secureCheck = "";

    @ViewChild('closebutton') closebutton;
    @ViewChild(AccountFormComponent) child: AccountFormComponent;

    constructor(
        private service : AccountsService,
        private dialog  : MatDialog,
        private notification : NotificationService
    ) { 
    }

    public getSearch(val, active = "", medCheck = "", secureCheck = "")
    {

        if(active=="")
        {
          if(this.isActive.value==true)
          {
              this.active = "1";
          }else{
              this.active = "";
          }
        }else{
          this.active = active
        }

        if(medCheck=="")
        {
          if(this.mediaCheck.value==true)
          {
            this.medCheck = "1";
          }else{
            this.medCheck = "";
          }
        }else{
          this.medCheck = medCheck
        }

        if(secureCheck=="")
        {
          if(this.secureCX.value==true)
          {
            this.secureCheck = "1";
          }else{
            this.secureCheck = "";
          }
        }else{
          this.secureCheck = secureCheck
        }

          this.service.searchAccounts(this.paginate.currentPage, val, this.active, this.medCheck, this.secureCheck).subscribe((data) => {
            this.paginate = this.service.getPaginate();
            this.accounts = data;
          });
    }

    public searchValue(event)
    {
        if(event.checked)
        {
            this.active = '1';
        }else{
          this.active = '';
        }
        this.getSearch(this.filter.value, this.active)
    }

    public searchMedia(event)
    {
        if(event.checked)
        {
            this.medCheck = '1';
        }else{
          this.medCheck = '';
        }
        this.getSearch(this.filter.value,"", this.medCheck)
    }

    public searchSecure(event)
    {
        if(event.checked)
        {
            this.secureCheck = '1';
        }else{
          this.secureCheck = '';
        }
        this.getSearch(this.filter.value, "", "", this.secureCheck)
    }

    ngOnInit(): void { 
      this.loadAccounts();

      this.filter.valueChanges.subscribe(val => {
        this.getSearch(val);
      });
    }

    /* 
    * Load the hostfile settings of the account
    *
    */
  public loadAccounts(){
    const self = this;
    this.service.query(this.pageNo ).subscribe( response => {
        self.accounts = response['data'] as Array<Account>;
        self.paginate = response as IPaginate;
        self.paginate['perPage'] = response['per_page'];
    });
  }

   /* 
    * Load the hostfile settings of the account
    *
    */
  public loadHostfile( account ){
    const modal = this.dialog.open( HostfileComponent, { 
      minWidth: '1024px',
      minHeight : '850px'
    });

    modal.componentInstance['account'] = account;
  }

   /* 
    * Show popup delete confirmation
    *
    */
  public confirmDelete( accountID ){
      const title = "Delete Account";
      const msg   = "Are you sure you want to delete the selected account?";
      const self  = this;
      this.notification.confirm(title, msg).then( result => {
          if( !result['isConfirmed'] )
            return;

          self.notification.success("Successfully deleted");
          self.service.delete( accountID ).then ( result => {
            self.loadAccounts();
          });
          
      });
  }

  /* 
    * Change page actions
    *
    */
  public showAccountForm( editMode, selectedAccount ){
    this.editMode = editMode;
    this.selectedAccount = selectedAccount;
  }

  /* 
    * Save Account
    *
    */
  public save( params ){
    const self = this;
    if( !this.editMode )
      this.service.add( params ).then( () => {
        self.loadAccounts();
      });

    else 
      this.service.update(params, this.selectedAccount['id'] ).then( () => {
        self.loadAccounts();
      });

    this.closebutton._elementRef.nativeElement.click();
    
    this.notification.success("Account successfully saved!");
  }

   /* 
    * Change page actions
    *
    */
  public pageChanged( event ){
    this.pageNo = event['pageIndex'] + 1;
    this.loadAccounts();
  }
  
  search_value = '';

}
