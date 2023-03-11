import { Component, OnInit } from '@angular/core';
import { Router } from "@angular/router";
import { AuthService } from '../../../../core/services/auth.services';

@Component({
    selector: 'logout',
    templateUrl: 'logout.page.html',

})
export class LogoutPage implements OnInit {
    constructor(
        private router: Router,
        private service: AuthService
    ) { }

    ngOnInit() {
        console.log("LOGOUT PAGE");
        this.service.logout();
        window.location.href = "/login";
    }


}