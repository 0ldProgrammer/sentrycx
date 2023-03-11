import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from "@angular/router";
import { Router } from "@angular/router";
import { UserConfigService } from '@app/services';
import { AuthService } from '../../../../core/services/auth.services';
import { environment } from '@env/environment';

@Component({
    selector: 'sso-login',
    templateUrl: 'sso-login.page.html',
    styleUrls: ['./sso-login.page.css']
})
export class SSOLoginPage implements OnInit {
    public userToken;
    public isLoading;
    public errorMessage = '';
    private messageList = {
        '401': 'Single Sign On authentication expired. Please try logging in again.',
        '404': "Unable to login your account. You don't have access to this application. Contact System Administrators",
        '500' : 'Single Sign On authentication expired. Please try logging in again.'
    }

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private service: AuthService,
        private config : UserConfigService
    ) { }


    ngOnInit() {
        // this._checkIfAlreadyLoggedIn();
        this.isLoading = true;
        var self = this;
        var check = localStorage.getItem('token');
        var user = localStorage.getItem('logged-in-user');

        var token = this.route.snapshot.queryParamMap.get('authorization');
        this.userToken = token;

        this.service.validate(token).subscribe((data) => {
            console.log("LOGGEDIN DATA", data)
            self.service.setUser(data['token'], data['user']);
            console.log('data', data)
            data['config'].forEach( (config) => {
                self.config.set( config['name'], JSON.parse( config['value'] ) );
            });
            window.location.href = '/dashboard/connected-agents';
        }, (error) => {
            console.log("ERROR", error)
            self.isLoading = false;
            if( error.status == 404 ) {
                self.errorMessage = "Oops! You don't have access to this site."
            }

            // switch( error.status){
            //     case 500 :
            //         self.errorMessage = "Oops! Something went wrong. Please try again."
            //         break;
            //     case 404 :
            //         self.errorMessage = "Oops! You don't have access to this site."
            //         break;

            // }
            self.errorMessage = self.messageList[error.status];

        })
    }

    public downloadApp(){
        window.location.href = environment.apiURL + `/auth/desktop-app-download?token=${this.userToken}`;
    }

}
