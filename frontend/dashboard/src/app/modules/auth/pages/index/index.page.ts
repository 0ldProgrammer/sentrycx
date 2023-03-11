import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../../../core/services/auth.services';

@Component({
    selector: 'index',
    templateUrl: './index.page.html'
})
export class IndexPage implements OnInit {
    constructor( private service : AuthService ) { }

    ngOnInit(): void { 
        console.log("INDEX PAGE")

        if( !this.service.isLogin() ){
            window.location.href = '/auth/login';
            return;
        }

        window.location.href = '/dashboard/summary';
    }
}
