import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';
declare const ORACLE_SERVICE_CLOUD: any;


@Injectable()
export class AuthService {
    private sessionToken: Observable<any> = new Observable;

    constructor(private http: HttpClient) { }

    public getAuthUrl(){
      return this.http.get(environment.apiURL + "/auth/auth-url");
    }

    public validate(authorizationToken) {
      const httpOptions = {
          headers: new HttpHeaders({
              'Authorization': `Bearer ${authorizationToken}`
          })
      };

      return this.http.get(environment.apiURL + "/auth/validate", httpOptions);
  }


    public login(authorizationToken) {
        const httpOptions = {
            headers: new HttpHeaders({
                'Authorization': `Bearer ${authorizationToken}`
            })
        };

        return this.http.get(environment.apiURL + "/auth/login", httpOptions);
    }


    public getTokenHeader(){
      const bearerToken = this.getToken();
      const header = new HttpHeaders({
        'Authorization': `Bearer ${bearerToken}`
      });

      return header;
    }

    public isLogin(): boolean {
        console.log("LOGGED IN", localStorage.getItem('logged-in') == 'TRUE' );
        return (localStorage.getItem('logged-in') == 'TRUE');
    }

    public getToken(){
      return localStorage.getItem('token');
    }

    public setUser(token: string, user: any): void {
        localStorage.setItem('token', token);
        localStorage.setItem('logged-in-user', JSON.stringify(user));
        localStorage.setItem('logged-in', 'TRUE');
    }

    public getUser() {
        return JSON.parse(localStorage.getItem('logged-in-user'));
    }

    public isAllowed( scopeName ){
        const loggedInUser = this.getUser();

        const userAccess   = loggedInUser['scope_access'] as String;

        if( !userAccess ) return false;

        return userAccess.includes( scopeName );

    }

    public logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('logged-in-user');
        localStorage.removeItem('logged-in');
    }
}


