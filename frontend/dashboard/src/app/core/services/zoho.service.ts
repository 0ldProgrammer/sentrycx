import { Injectable } from '@angular/core';
import { environment } from '@env/environment';
import { HttpClient } from '@angular/common/http';
import { AuthService } from '@app/services';

@Injectable()
export class ZohoService {
  private apiURL = environment.apiURL;

  constructor(
    private http : HttpClient,
    private auth : AuthService 
  ){}

  /*
   * Generates OAUTH URL needed to access zoho
   */
  public oauth( state ){
    const params = { state : state };
    return this.http.get(`${this.apiURL}/zoho/oauth`, { params : params } );
  }

  /*
   * Generate accessToken
   */
  public accessToken( code ){
    const params = { code : code };
    return this.http.post(`${this.apiURL}/zoho/access-token`, params ).toPromise();
  }

  /*
   * Create RDP Session
   */
  public startSession( accessToken, state ){
    const secureHeader = this.auth.getTokenHeader();
    const params = {
      access_token : accessToken,
      state : state
    };
    return this.http.post(`${this.apiURL}/zoho/remote-session`, params, { headers : secureHeader }).toPromise();
  }

}
