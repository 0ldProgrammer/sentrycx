import { Component, OnInit, ViewChild } from '@angular/core';
import { MaintenanceService, ToastService, NotificationService } from '@app/services';
import { PaginateFactory } from '@app/factory/index';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { IPaginate, IAccount,IVlan } from '@app/interfaces/index';
import swal from 'sweetalert2';
import { Router } from '@angular/router';
import { identifierModuleUrl } from '@angular/compiler';


@Component({
    selector: 'vlan-mapping',
    templateUrl: './vlan-mapping.page.html',
    styleUrls : ['./vlan-mapping.page.css']
})

export class VlanMappingPage implements OnInit {

    public isLoading : Boolean = true;
    public pageNo = 1;
    public paginate : IPaginate = PaginateFactory.init() ;
    public mappingData : Array<IVlan> = [];
    public accounts: Array<IAccount> = [];
    public selectedData : IVlan;
    public editMode : Boolean = false;

    filter = new FormControl('');


  AddMappingForm = new FormGroup({
    name: new FormControl('', Validators.required),
    subnet: new FormControl('', Validators.required),
    account: new FormControl(''),
    is_active: new FormControl('')
  });
  @ViewChild('closebutton') closebutton;

    constructor(
        private service : MaintenanceService,
        private toast: ToastService,
        private notification : NotificationService
    ) {}

    ngOnInit(): void { 
        const self = this;
        this.paginate = this.service.getPaginate();
        this.loadMappingData();

        // this.filter.valueChanges.subscribe(val => {
        //     this.searchValue(val)
        // });
    }
    
    
    public searchValue()
    {
        const self = this;
        this.service.getVlanMappingList(this.paginate.currentPage, this.filter.value).subscribe((data) => {
            this.paginate = this.service.getPaginate();
            self.mappingData = data;
            this.isLoading = false;
            });
        // console.log(val)
    }

    loadMappingData() {
        const self = this;
        this.service.getAccounts().subscribe((data) => {
            self.accounts = data;
        });
            this.service.getVlanMappingList(this.paginate.currentPage, this.filter.value).subscribe((data) => {
            
            this.paginate = this.service.getPaginate();
            self.mappingData = data;
            this.isLoading = false;
        });
    }


    /*
     * Checks if the individual fields are valid
     */
    isInvalid(fieldName){
        const fieldControl = this.AddMappingForm.controls[fieldName];
  
        return (fieldControl.touched && fieldControl.invalid);
      }


   /* 
    * Change page actions
    *
    */
   public pageChanged(pageNo) {
     this.paginate.currentPage = pageNo.pageIndex + 1;
     this.loadMappingData();
   }

   showMappingForm(editMode, mappingData)
   {
       this.editMode = editMode;
        if(this.editMode){
            this.selectedData = mappingData;
            this.AddMappingForm.setValue({
                name: this.selectedData.name,
                subnet: this.selectedData.subnet,
                account: this.selectedData.acount,
                is_active: this.selectedData.is_active,
            });
        }else{
            this.AddMappingForm.reset()
        }



   }

   public searchEmptyValue()
   {
       const self = this;
       if(this.filter.value=="")
       {
           this.loadMappingData();
       }
       
   }

   public emptySearch()
   {
       console.log('hey')
       this.filter.setValue('');
       this.loadMappingData();

   }

   public confirmDelete( mappingID ){
        const title = "Delete Mapping";
        const msg   = "Are you sure you want to delete the selected mapping?";
        const self  = this;
        this.notification.confirm(title, msg).then( result => {
            if( !result['isConfirmed'] )
                return;

            self.notification.success("Successfully deleted");
            self.service.deleteVlanMapping( mappingID ).then ( result => {
                self.loadMappingData();
            });
            
        });
    }


   onSubmitMapping(event) {
        const self = this;
        if( !this.editMode )
            this.service.addVlanMapping(this.AddMappingForm.value).then((data) => {
                this.toast.showNotification('top', 'right', 'Mapping Successfully Added!', 'success');
                console.log(data)

            });

        else 
            this.service.updateVlanMapping(this.AddMappingForm.value, this.selectedData.id ).then((data) => {
                console.log(data)
                this.toast.showNotification('top', 'right', 'Mapping Successfully Updated!', 'success');
            });


        this.loadMappingData();

        this.closebutton._elementRef.nativeElement.click()

   }
  

  search_value = '';
}