import { Location } from '@angular/common';
import { UserFormComponent } from './../../components/user-form/user-form.component';
import { Component, OnInit, ViewChild } from '@angular/core';

import { UserService, ToastService, NotificationService, AuthService } from '@app/services';
import { IUser, IPaginate } from '@app/interfaces';
import { FormControl } from '@angular/forms';
import swal from 'sweetalert2';
import { replace } from 'lodash';

declare const $: any;
@Component({
  selector: 'users-list-page',
  templateUrl: './users-list.page.html',
  styleUrls: ['./users-list.page.css']
})
export class UsersListPage implements OnInit {
  public userDetails: IUser;
  public isLoading: Boolean = true;
  public users: Array<IUser> = [];
  public paginate: IPaginate = this.service.getPaginate();
  public sites: Array<string>;
  public accounts: Array<string>;
  private userID;
  public filters : any = {};
  searchBar = new FormControl('');
  mode: string = "Add";
  search_value = '';
  filter = new FormControl('');
  public hasFiltered : Boolean = false;

  public hasFlagFilttered : Boolean = false;

  public filterOptions : any = {
    fields  : [
      { key : 'location', name : 'Location', type : 'select' },
      { key : 'account_access', name : 'Account', type : 'select'}
    ],

    filters : {}

  }

  constructor(
    private service: UserService,
    private toast: ToastService,
    private notification: NotificationService,
    private auth : AuthService
    // private dialog    : MatDialog
  ) { 
  }

  /*
   * Handles on loading of component/page
   */
  ngOnInit() {
    this._checkAccess();
    this.loadUsers();
    this._loadSites();
    this._loadAccounts();
    this._loadFilters();

    this.searchBar.valueChanges.subscribe(val => {
      this.searchValue(val)
    });
  }

  /*
   * Checks the user access if allowed to open users page
   */
  public _checkAccess() {
    if( !this.auth.isAllowed( 'users:scope' ) )
      window.location.href = "/dashboard/summary";
  }

  /*
   * Loads the users
   */
  public loadUsers() {
    const self = this;
    self.isLoading = true;
    this.service.getUsers(this.paginate.currentPage, this.filters).subscribe((result) => {
      self.users = self.service.getData();
      self.paginate = self.service.getPaginate();
      self.isLoading = false;
    });
  }
    /*
   * Loads the sites
   */
  _loadSites() {
    const self = this;
    this.service.getSites().subscribe((response) => {
      self.sites = response['data'];
    });
  }
    /*
   * Loads the accounts
   */
  _loadAccounts() {
      const self = this;
      this.service.getAccounts().subscribe((response) => {
        self.accounts = response['data'];
      });
    }

      /*
   * Retrieve all the list of filters available for searching
   *
   */
  private _loadFilters(){
    const self   = this;
    // const fields = ['location_name', 'msa_client' ];
    const fields = ['location', 'account_access' ];

    this.service.getFilters( fields ).subscribe( data  => {
      const filters = data['data'];

      self.filterOptions['filters']['location']    = filters['location'];
      self.filterOptions['filters']['account_access']    = filters['account_access'];
    });
  }

  /*
   *  Handles the changing of page
   */
  public pageChanged(pageNo) {
    this.paginate.currentPage = pageNo.pageIndex + 1;
    this.loadUsers();
  }

  public searchValue(val)
  {
      const self = this;

      this.isLoading = true;
  
      this.service.searchUsers(val, this.paginate.currentPage, this.filters ).subscribe( (data) => {
        self.users = data;
        self.paginate = self.service.getPaginate();
        self.isLoading = false;
      });
  }

    /*
   * Handles the search filter function
   */
    public search( data  ){
      const self = this;
      const params = data.params;
  
      Object.keys( params ).forEach( (val, index ) => {
        if( !params[val] ) return;
  
        const filterKeyValue = [];
  
        filterKeyValue[`${val}[]`] = params[val];
  
        self.filters = Object.assign( self.filters, filterKeyValue );
        
      });
  
      this.loadUsers();
      self.notification.success('Users has been filtered.');
      self.hasFiltered = true;
    }
  
    /*
     * Handles resetting the search
     */
    public reset(){
      // this.router.navigate(['/users'] )
      this.hasFiltered = false;
      this.filters = {};
      this.loadUsers();
    }

  displayName: string;
  deleteConfirm(user) {
    this.userID = user['id'];
    this.displayName = user['firstname'];
    
  }
  deleteUserConfirm() {
    const self = this;
    this.service.deleteUser(this.userID).then((response) => {
      this.toast.showNotification('top', 'right', response['message'], 'success');
      self.loadUsers();
    });
   
  }
  /*
   * Process the execution of deleting user
   */
  private _deleteUser(userID) {
    const self = this;
    this.service.deleteUser(userID).then((response) => {
      self.toast.show('bg-success text-light', response['message']);
      self.loadUsers();
    });
  }
  @ViewChild(UserFormComponent) child: UserFormComponent;
  addUser() {
    this.loadUsers();
    this.mode = "Add";
    this.child.clear();
  }
  @ViewChild('closebutton') closebutton;
  /*
 * Handles the saving of form
 */
  save(userParams, mode) {
    if (mode == "Add") {
      this.service.createUser(userParams).then((data) => {
        this.loadUsers();
        this.toast.showNotification('top', 'right', data['message'], 'success');
      });
    }
    if (mode == "Edit") {
      this.service.updateUser(userParams, userParams.id).then((data) => {
        this.loadUsers();
        this.toast.showNotification('top', 'right', data['message'], 'success');
      });
    }
    this.closebutton._elementRef.nativeElement.click();
  }

  editCode(row) {
    // const SCOPE_REF = [
    //   { id : 'admin:connected-itss', name : 'Connected ITSS' },
    //   { id : 'admin:lock-wipeout', name : 'PC Lock / Wipeout' },
    //   { id : 'admin:audit-logs', name : 'Audit Logs' },
    //   { id : 'admin:unresolve-issues', name : 'Unresolve Issues' },
    //   { id : 'admin:idle-agents', name : 'Idle Agents' },
    //   { id : 'admin:unlisted', name : 'Unlisted' },
    //   { id : 'users:scope', name : 'User Access' },
    // ];
    this.mode = "Edit";
    const self = this;
    self.isLoading = true;
    this.service.getUser(row.id).subscribe((response) => {
      let scopeName = response['data']['scope_access'];

      // SCOPE_REF.forEach( (ref ) => {
      //   scopeName = replace( scopeName, ref['id'], ref['name']);
      // });

      self.userDetails = response['data'];
      self.userDetails['scope_access'] = scopeName.split(','); 
      self.isLoading = false;
    });
  }
  showSwal(row) {
    swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger',
      },
      confirmButtonText: 'Yes, delete it!',
      buttonsStyling: false
    }).then((result) => {
      if (result.value) {
        const self = this;
        this.service.deleteUser(row.id).then((response) => {
          this.toast.showNotification('top', 'right', response['message'], 'success');
          self.loadUsers();
        });
        swal.fire(
          {
            title: 'Deleted!',
            text: row.firstname + ' has been deleted.',
            icon: 'success',
            customClass: {
              confirmButton: "btn btn-success",
            },
            buttonsStyling: false
          }
        )
      }
    })
  }
}
