import { Component, OnInit, ViewChild } from '@angular/core';
import { MaintenanceService, ToastService, NotificationService } from '@app/services';
import { IPaginate, IApplicationsList, ISoftwareUpdates } from '@app/interfaces/index';
import { PaginateFactory } from '@app/factory/index';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import swal from 'sweetalert2';
import { Router } from '@angular/router';
import { identifierModuleUrl } from '@angular/compiler';


@Component({
    selector: 'software-update',
    templateUrl: './software-update.page.html',
    styleUrls : ['./software-update.page.css']
})

export class SoftwareUpdatePage implements OnInit {

    public isLoading : Boolean = true;
    public pageNo = 1;
    public paginate : IPaginate = PaginateFactory.init() ;
    public softwareUpdatesList : Array<ISoftwareUpdates> = [];
    public editMode : Boolean = false;
    filter = new FormControl('');


  @ViewChild('closebutton') closebutton;

    constructor(
        private service : MaintenanceService,
        private toast: ToastService,
        private notification : NotificationService
    ) {}

    ngOnInit(): void { 
        const self = this;
        this.paginate = this.service.getPaginate();
        this.loadSoftwareUpdatesData();
    }
    
    public searchValue()
    {
        const self = this;
        this.service.getSoftwareUpdatesList(this.paginate.currentPage, this.filter.value).subscribe((data) => {
            this.paginate = this.service.getPaginate();
            self.softwareUpdatesList = data;
            this.isLoading = false;
        });
        
    }
    
    public searchEmptyValue()
    {
        const self = this;
        if(this.filter.value=="")
        {
            this.loadSoftwareUpdatesData();
        }
        
    }

    public emptySearch()
    {
        this.filter.setValue('');
        this.loadSoftwareUpdatesData();
    }

    public onSearchIcon() {
		this.searchValue();
	}

    public loadSoftwareUpdatesData() {
        const self = this;

        this.service.getSoftwareUpdatesList(this.paginate.currentPage, this.filter.value).subscribe((data) => {
            this.paginate = this.service.getPaginate();
            self.softwareUpdatesList = data;
            this.isLoading = false;
        });
    }

    public executeNow(data){
        swal.fire({
            title: 'Are you sure?',
            text: "You are about to Execute the Selected Update",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, execute it!'
          }).then((result) => {
            if (result.isConfirmed) {
                swal.fire(
                    'Success!',
                    'It is now currently running in the background.',
                    'success'
                )
                this.service.executeSoftwareUpdate(data).subscribe(() => {
                });
            }
          })
    }

   /* 
    * Change page actions
    *
    */
   public pageChanged(pageNo) {
     this.paginate.currentPage = pageNo.pageIndex + 1;
     this.loadSoftwareUpdatesData();
   }

  search_value = '';

}