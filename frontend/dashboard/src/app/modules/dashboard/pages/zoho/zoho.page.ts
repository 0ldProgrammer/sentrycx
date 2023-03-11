import { Component, OnInit } from '@angular/core';
import { ZohoService } from '@app/services';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector : 'zoho',
  templateUrl : './zoho.page.html',
  styleUrls : ['./zoho.page.css']
})
export class ZohoPage implements OnInit {

  private code : String;

  private accessToken : String;

  public hasError : Boolean = false;

  public state : String;

  private msgList : any = {
     authorize : 'Connecting to Zoho..',
     authenticate : 'Validating your access..',
     session : 'Starting Zoho RDP Session..'
  };

  public msg : String;

  constructor(
    private service : ZohoService,
    private route : ActivatedRoute
   ){}

  ngOnInit(){
    this.code  = this.route.snapshot.queryParamMap.get('code');
    this.state = this.route.snapshot.queryParamMap.get('state');

    if( !this.code ){
      this._authorize();
      return;
    }

    this._authenticate();
  }


  /*
   * Redirect to OAuth URL
   */
  private _authorize(){
    this.msg  = this.msgList['authorize'];

    this.service.oauth( this.state ).subscribe( (response) => {
      window.location.href = response['URL'];
    });
  }


  /*
   * Generate Access Token
   */
  private _authenticate(){
    const self = this;
    this.msg  = this.msgList['authenticate'];
    this.service.accessToken( this.code ).then( (response) => {
      self.accessToken = response['access_token'];
      self._accessSession();
    }, (error) => {
      self.hasError = true;
    });
  }

  /*
   * Redirect to RDP Session
   */
  private _accessSession(){
    const self = this;
    this.msg  = this.msgList['session'];
    this.service.startSession( this.accessToken, this.state ).then( (response) => {
      window.location.href = response['URL'];
    }, (error) => {
      self.hasError = true;
    });
  }
}
