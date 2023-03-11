import { Component, OnInit, ViewChild } from '@angular/core';
import { MaintenanceService, ToastService, NotificationService } from '@app/services';
import { IPaginate,ISubnet } from '@app/interfaces/index';
import { PaginateFactory } from '@app/factory/index';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import swal from 'sweetalert2';
import { Router } from '@angular/router';
import { identifierModuleUrl } from '@angular/compiler';


@Component({
    selector: 'subnet-mapping',
    templateUrl: './subnet-mapping.page.html',
    styleUrls : ['./subnet-mapping.page.css']
})

export class SubnetMappingPage implements OnInit {

    public isLoading : Boolean = true;
    public pageNo = 1;
    public paginate : IPaginate = PaginateFactory.init() ;
    public mappingData : Array<ISubnet> = [];
    public selectedData : ISubnet;
    public editMode : Boolean = false;
    public locationList : any;
    public accountList : any;

    filter = new FormControl('');


  AddMappingForm = new FormGroup({
    subnet: new FormControl('', Validators.required),
    location: new FormControl(''),
    site: new FormControl(''),
    client: new FormControl(''),
    type: new FormControl('', Validators.required)
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
        this.getClientLocationList();
        // this.getClientList();

    }
    
    getClientLocationList()
    {
        const self = this;
        this.service.getClientLocationList().subscribe((data)=>{
            self.locationList = data['locationList'];
            self.accountList = data['clientList'];

        })
    }
    
    public searchValue()
    {
        const self = this;
        this.service.getMappingList(this.paginate.currentPage, this.filter.value).subscribe((data) => {
            this.paginate = this.service.getPaginate();
            self.mappingData = data;
            this.isLoading = false;
        });
        
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



    loadMappingData() {
        const self = this;
            this.service.getMappingList(this.paginate.currentPage, this.filter.value).subscribe((data) => {
            
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
                subnet: this.selectedData.subnet,
                location: this.selectedData.location,
                site: this.selectedData.site,
                client: this.selectedData.client,
                type: this.selectedData.type,
            });
        }else{
            this.AddMappingForm.reset()
        }



   }

   public confirmDelete( mappingID ){
        const title = "Delete Mapping";
        const msg   = "Are you sure you want to delete the selected mapping?";
        const self  = this;
        this.notification.confirm(title, msg).then( result => {
            if( !result['isConfirmed'] )
                return;

            self.notification.success("Successfully deleted");
            self.service.deleteMapping( mappingID ).then ( result => {
                self.loadMappingData();
            });
            
        });
    }


   onSubmitMapping(event) {
        const self = this;
        if( !this.editMode )
            this.service.addMapping(this.AddMappingForm.value).then((data) => {
                this.toast.showNotification('top', 'right', 'Mapping Successfully Added!', 'success');

            });

        else 
            this.service.updateMapping(this.AddMappingForm.value, this.selectedData.id ).then((data) => {
                this.toast.showNotification('top', 'right', 'Mapping Successfully Updated!', 'success');
            });


        this.loadMappingData();

        this.closebutton._elementRef.nativeElement.click()

   }
  

  search_value = '';
}