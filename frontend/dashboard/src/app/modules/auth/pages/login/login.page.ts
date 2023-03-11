import { Component, OnInit } from '@angular/core';
import { AuthService } from '@app/services';
import { environment } from '@env/environment';

@Component({
    selector: 'login',
    templateUrl: 'login.page.html',
    styleUrls: ['./login.page.css']
})
export class LoginPage implements OnInit {
    public authLink ;
    constructor( private service : AuthService ) {}

    ngOnInit() {
        this.authLink = environment.authURL;
    }

    public redirectToAuth( event : any ) : void {
        event.stopPropagation();
        this.service.getAuthUrl().subscribe( data => {
          window.location.href = data['url'];
        });
    }
}
