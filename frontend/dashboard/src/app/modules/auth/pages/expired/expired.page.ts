import { Component } from '@angular/core';
import { AuthService } from '@app/services';
import { environment } from '@env/environment';

@Component({
  selector : 'expired-page',
  templateUrl : './expired.page.html',
  styleUrls : ['./expired.page.css']
})
export class ExpiredPage {

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
