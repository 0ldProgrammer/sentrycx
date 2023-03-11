import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { WorkstationService, NotificationService} from '@app/services/index';
import * as _ from 'lodash';
import swal from 'sweetalert2';
import { environment } from '@env/environment';
import { CDK_CONNECTED_OVERLAY_SCROLL_STRATEGY } from '@angular/cdk/overlay/overlay-directives';


@Component({
    selector: 'disable-email',
    templateUrl: 'disable-email.page.html'
})
export class DisableEmailPage implements OnInit {

    public checkEmail;

    public initialSpeedtestValue : boolean = true;
    public initialApplicationValue : boolean = true;
    public initialWorkstationValue : boolean = true;
    public initialUtilizationValue : boolean = true;
    public initialRequiredAndRestrictedValue : boolean = true;

    public disabled : boolean = false;
    

    constructor(
        private service : WorkstationService
    ) {}

    ngOnInit() {
        this.checkEmailReports();
     }

    public checkEmailReports() {
        const self = this;
        let speedtest : boolean = true;
        let application : boolean = true;
        let offline : boolean = true;
        let utilization : boolean = true;
        let required : boolean = true;
        let email;

        this.service.getReportsTypeOnEmail().subscribe((data) => {
            if (data['status'] != 'error' && data['status'] != 'email_only'){
                if (data['data'].length > 0) {
                    data['data'].map(function (el) {
                        
                        email = el.email;
    
                        switch(el.report_type_id) {
                            case 1:
                                speedtest = false; 
                            break;
                            case 2:
                                application = false;
                            break;
                            case 3:
                                offline = false;
                            break;
                            case 4:
                                utilization = false;
                            break;
    
                            default:
                                required = false;
                            break;
                        }
                });
                }
    
                this.initialSpeedtestValue = speedtest;  
                this.initialApplicationValue = application;  
                this.initialWorkstationValue = offline;  
                this.initialUtilizationValue = utilization;  
                this.initialRequiredAndRestrictedValue = required;
                this.checkEmail = email; 
            } else if ((data['status'] == 'email_only')) {
                this.checkEmail = data['email'];
                this.initialSpeedtestValue = speedtest;  
                this.initialApplicationValue = application;  
                this.initialWorkstationValue = offline;  
                this.initialUtilizationValue = utilization;  
                this.initialRequiredAndRestrictedValue = required; 
            } else {
                this.disabled = true;
            }

        });
   
        
    }

    public checkSpeedtest(e) {
        this.initialSpeedtestValue = e.checked;
    }

    public checkApplication(e){
        this.initialApplicationValue = e.checked;
    }

    public checkWorkstation(e){
        this.initialWorkstationValue = e.checked;
    }

    public checkUtilization(e){
        this.initialUtilizationValue = e.checked;
    }

    public checkRestrictedAndRequired(e){
        this.initialRequiredAndRestrictedValue = e.checked;
    }

    public executeNow(){

        let caption;
        let confirmButton;

        let objectData = {
            'speedtest' : this.initialSpeedtestValue,
            'application' : this.initialApplicationValue,
            'offline' : this.initialWorkstationValue,
            'utilization' : this.initialUtilizationValue,
            'required' : this.initialRequiredAndRestrictedValue,
            'email' : this.checkEmail
        }
        
        if (this.initialSpeedtestValue || this.initialApplicationValue || this.initialWorkstationValue || this.initialUtilizationValue || this.initialRequiredAndRestrictedValue) {
            caption = "You are about to received Mail Notifications that were checked!";
            confirmButton = "Proceed!";
        } else {
            caption = "You have selected to disabled Mail Notifications!";
            confirmButton = "Disabled!";
        } 

        swal.fire({
            title: 'Are you sure?',
            text: caption,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButton
            }).then((result) => {
            if (result.isConfirmed) {
                this.service.updateMailNotification(objectData).subscribe(() => {
                    swal.fire(
                        'Success!',
                        'Mail Notifications has been successfully updated.',
                        'success'
                    );
                });
            }
        })
        
    }

}