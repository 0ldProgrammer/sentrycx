import { Component, OnInit, ViewChild } from '@angular/core';
import { MaintenanceService, ToastService, NotificationService } from '@app/services';
import { IPaginate, IMailNotifications } from '@app/interfaces/index';
import { PaginateFactory } from '@app/factory/index';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import swal from 'sweetalert2';
import { Router } from '@angular/router';
import { identifierModuleUrl } from '@angular/compiler';


@Component({
    selector: 'mail-notification',
    templateUrl: './mail-notification.page.html',
    styleUrls : ['./mail-notification.page.css']
})

export class MailNotificationPage implements OnInit {

    public isLoading : Boolean = true;
    public pageNo = 1;
    public paginate : IPaginate = PaginateFactory.init() ;
    public mailNotificationsList : Array<IMailNotifications> = [];
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
        this.loadMailNotificationsData();
    }
    
    public searchValue()
    {
        const self = this;
        this.service.getMailNotificationsList(this.paginate.currentPage, this.filter.value).subscribe((data) => {
            this.paginate = this.service.getPaginate();
            self.mailNotificationsList = data;
            this.isLoading = false;
        });
        
    }
    
    public searchEmptyValue()
    {
        const self = this;
        if(this.filter.value=="")
        {
            this.loadMailNotificationsData();
        }
        
    }

    public emptySearch()
    {
        this.filter.setValue('');
        this.loadMailNotificationsData();
    }

    public onSearchIcon() {
		this.searchValue();
	}

    public loadMailNotificationsData() {
        const self = this;

        this.service.getMailNotificationsList(this.paginate.currentPage, this.filter.value).subscribe((data) => {
            this.paginate = this.service.getPaginate();
            self.mailNotificationsList = data;
            this.isLoading = false;
        });
    }

    public updateStatus(data){

        let status = data.status_code ? 'Enable' : 'Disable';

        swal.fire({
            title: 'Are you sure?',
            text: 'You are about to ' + status + ' the Selected Report',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, ' +  status + ' it!'
          }).then((result) => {
            if (result.isConfirmed) {
                this.service.updateMailNotifications(data).subscribe(() => {
                    this.loadMailNotificationsData();
                    swal.fire(
                        'Success!',
                        'Status has been updated.',
                        'success'
                    )
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
     this.loadMailNotificationsData();
   }

  search_value = '';

}