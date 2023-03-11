import { Component, OnInit } from '@angular/core';
import { WorkstationService, AuthService } from '@app/services';
import { IPaginate } from '@app/interfaces/index';
import { PaginateFactory } from '@app/factory/index';
import { FormControl} from '@angular/forms';
import swal from 'sweetalert2';
import { Router } from '@angular/router';
import { identifierModuleUrl } from '@angular/compiler';

interface EventApprovalData {
    id:  number,
    event: string,
    agent_name: string,
    requested_by : string,
    status: string,
}

@Component({
    selector: 'event-approvals',
    templateUrl: './event-approvals.page.html',
    styleUrls : ['./event-approvals.page.css']
})

export class EventApprovalsPage implements OnInit {

    public isLoading : Boolean = true;
    public pageNo = 1;
    public paginate : IPaginate = PaginateFactory.init() ;
    public eventData : Array<EventApprovalData> = [];
    public selectedData : any = [];

    filter = new FormControl('');

    constructor(
        private service : WorkstationService,
        private auth : AuthService,
        private router :  Router
    ) {}

    ngOnInit(): void { 
        const self = this;
        this.loadEventApprovals()

        this.filter.valueChanges.subscribe(val => {
            this.searchValue(val)
        });
        this.checkSecureMenu();
    }


    checkSecureMenu()
    {
        if(!this.auth.isAllowed('event-approvals:page')){
            this.router.navigate(['/']);
        }

    }

    public onCheck(event, id, event_name, worker_id)
    {   
        const user = this.auth.getUser()
        var details = {
            event_name : event_name,
            status : true,
            approved_by : user.firstname,
            worker_id : worker_id
        }

        var eventDetails = {
            id : id,
            details : details
        }

        if(event.checked)
        {
            this.selectedData.push(eventDetails)
        }else{
            this.selectedData.splice(this.selectedData.indexOf(eventDetails), 1);
        }
        // console.log(this.selectedData)
    }

    public onApproveSelected()
    {
        swal.fire({
            title: 'Are you sure?',
            text: "You are about to Approve those selected request!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approved it!'
          }).then((result) => {
            if (result.isConfirmed) {

                for(let key of Object.keys(this.selectedData)){
                    let e = this.selectedData[key]
                    // console.log(e.details)
                    this.service.ApproveEvent(e.id, e.details).then((data) => {

                        this.loadEventApprovals();
                        
                        console.log(data)
                    })
                }
                swal.fire(
                    'Approved!',
                    'Your have successfully approve those selected request.',
                    'success'
                )
            }
          })
    }

    public searchValue(val)
    {
            this.service.searchEventApprovals(val).subscribe(response => {
                this.eventData = response['data'];
            });
        console.log(val)
    }

    public loadEventApprovals(){
        const self = this;
        this.service.getEventApprovals(this.pageNo).subscribe( response => {
            self.eventData = response['data'] as Array<EventApprovalData>;
            self.paginate = response as IPaginate;
            self.paginate['perPage'] = response['per_page'];

            console.log(self.eventData)
        });
    }

    public onApprove(event_id, event_name, worker_id)
    {
        const user = this.auth.getUser()
        const self = this;
        const details = {
            event_name : event_name,
            status : true,
            approved_by : user.firstname,
            worker_id : worker_id
          }
          swal.fire({
            title: 'Are you sure?',
            text: "You are about to Approve this request!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approved it!'
          }).then((result) => {
            if (result.isConfirmed) {
                this.service.ApproveEvent(event_id, details).then((data) => {
                    
                    swal.fire({
                      title: event_name+' Request',
                      text: 'Request Successfully Approved',
                      icon: 'success',
                      customClass:{
                        confirmButton: "btn btn-info",
                      },
                      buttonsStyling: false
                    });
                    this.loadEventApprovals();
                    console.log(data)
                })
            
            }
        })

    }


   /* 
    * Change page actions
    *
    */
   public pageChanged( event ){
        this.pageNo = event['pageIndex'] + 1;
        this.loadEventApprovals();
    }
  

  search_value = '';
}