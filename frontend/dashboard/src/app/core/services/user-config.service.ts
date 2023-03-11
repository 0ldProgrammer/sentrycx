import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { AuthService } from './auth.services';
import { environment } from '../../../environments/environment';

@Injectable()
export class UserConfigService {
    private apiURL = environment.apiURL;

    constructor( 
        private auth : AuthService,
        private http : HttpClient
    ){}

    /* 
     * Saves a user config to localStorage
     */
    public set(configName, data : any ){
        const userID = this.auth.getUser().id;

        const params = { config_name : configName, value : data };

        localStorage.setItem( configName, JSON.stringify(data) );

        this.http.post(`${this.apiURL}/maintenance/user/${userID}/config`, params ).toPromise();
    }

    /*
     * Retreive the user config from localStorage
     */ 
    public get(configName){
        return JSON.parse( localStorage.getItem(configName))
    }
}