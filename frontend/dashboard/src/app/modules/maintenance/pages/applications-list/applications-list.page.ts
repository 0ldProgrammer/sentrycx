import { Component, OnInit, ViewChild } from '@angular/core';
import { MaintenanceService, ToastService, NotificationService } from '@app/services';
import { IPaginate, IApplicationsList } from '@app/interfaces/index';
import { PaginateFactory } from '@app/factory/index';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import swal from 'sweetalert2';
import { Router } from '@angular/router';
import { identifierModuleUrl } from '@angular/compiler';


@Component({
    selector: 'applications-list',
    templateUrl: './applications-list.page.html',
    styleUrls : ['./applications-list.page.css']
})

export class ApplicationsListPage implements OnInit {

    public isLoading : Boolean = true;
    public pageNo = 1;
    public paginate : IPaginate = PaginateFactory.init() ;
    public applicationsData : Array<IApplicationsList> = [];
    public selectedData : IApplicationsList;
    public editMode : Boolean = false;
    public accountList: any;
    public operation: any;
    filter = new FormControl('');


  AddApplicationForm = new FormGroup({
    application: new FormControl('', Validators.required),
    type: new FormControl('', Validators.required),
    account_affected: new FormControl('')
  });

  public typeData = [
    { type: 'Required' },
    { type: 'Restricted' }
];

  @ViewChild('closebutton') closebutton;

    constructor(
        private service : MaintenanceService,
        private toast: ToastService,
        private notification : NotificationService
    ) {}

    ngOnInit(): void { 
        const self = this;
        this.paginate = this.service.getPaginate();
        this.loadApplicationData();
        this.getAccountList();
    }
    
    getAccountList()
    {
        const self = this;
        this.service.getAccountList().subscribe((data)=>{
            self.accountList = data['accountList'];
        })
    }
    
    public searchValue()
    {
        const self = this;
        this.service.getApplicationsList(this.paginate.currentPage, this.filter.value).subscribe((data) => {
            this.paginate = this.service.getPaginate();
            self.applicationsData = data;
            this.isLoading = false;
        });
        
    }
    
    public searchEmptyValue()
    {
        const self = this;
        if(this.filter.value=="")
        {
            this.loadApplicationData();
        }
        
    }

    public emptySearch()
    {
        this.filter.setValue('');
        this.loadApplicationData();
    }

    public onSearchIcon() {
		this.searchValue();
	}

    loadApplicationData() {
        const self = this;
            this.service.getApplicationsList(this.paginate.currentPage, this.filter.value).subscribe((data) => {
            this.paginate = this.service.getPaginate();
            self.applicationsData = data;
            this.isLoading = false;
        });
    }

    /*
     * Checks if the individual fields are valid
     */
    isInvalid(fieldName){
        const fieldControl = this.AddApplicationForm.controls[fieldName];
  
        return (fieldControl.touched && fieldControl.invalid);
      }

   /* 
    * Change page actions
    *
    */
   public pageChanged(pageNo) {
     this.paginate.currentPage = pageNo.pageIndex + 1;
     this.loadApplicationData();
   }

   showApplicationForm(editMode, applicationsData)
   {
       let accounts_array;
       this.editMode = editMode;
        if(this.editMode){
            this.selectedData = applicationsData;
            if (this.selectedData.account_affected) {
                accounts_array = this.selectedData.account_affected.split(",");
            } else {
                accounts_array = [];
            }

            this.AddApplicationForm.setValue({
                application: this.selectedData.name,
                type: this.selectedData.type,
                account_affected: accounts_array
            });
            this.operation = 'Edit';
        }else{
            this.AddApplicationForm.reset();
            this.operation = 'Add';
        }
   }

   public confirmDelete( applicationID ){
        const title = "Delete Application";
        const msg   = "Are you sure you want to delete the selected application?";
        const self  = this;
        this.notification.confirm(title, msg).then( result => {
            if( !result['isConfirmed'] )
                return;

            self.notification.success("Successfully deleted");
            self.service.deleteApplication( applicationID ).then ( result => {
                self.loadApplicationData();
            });
            
        });
    }


   onSubmitApplication(event) {
        const self = this;
   
        if( !this.editMode ) {
            this.service.addApplication(this.AddApplicationForm.value).then((data) => {
                this.toast.showNotification('top', 'right', 'Application Successfully Added!', 'success');
                self.loadApplicationData();

            });

        } else { 
            this.service.updateApplication(this.AddApplicationForm.value, this.selectedData.id ).then((data) => {
                this.toast.showNotification('top', 'right', 'Application Successfully Updated!', 'success');
                self.loadApplicationData();
            });

        }
        
        this.closebutton._elementRef.nativeElement.click()
   }

  search_value = '';

    onAccountChange( account_affected ){
        this.AddApplicationForm.controls['account_affected'].setValue( account_affected.value );
    }
}