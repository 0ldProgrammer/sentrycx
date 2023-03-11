import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '@env/environment';
import { Observable } from 'rxjs';
import { map,catchError } from 'rxjs/operators';
import { AuthService } from './auth.services';

@Injectable()
export class EventLogsService {
    private apiURL = environment.apiURL;

    constructor (
        private http : HttpClient,
        private auth : AuthService
    ){}
    
    /*
     * Initialize log file
     */ 
    init( workerID, params ){
        const secureHeader = this.auth.getTokenHeader();
        return this.http.post(`${this.apiURL}/workstation/logs/${workerID}/init`, params, { headers : secureHeader } ).toPromise();
    }

    /*
     * Initialize log file
     */ 
    all( workerID ){
        return this.http.get(`${this.apiURL}/workstation/logs/${workerID}` );
    }
}