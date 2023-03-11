import { Injectable } from '@angular/core';
import { HttpClient,HttpParams } from '@angular/common/http';
import { environment } from '@env/environment';
import { AuthService } from './auth.services';
import { IPaginate } from '@app/interfaces';
import { PaginateFactory } from '@app/factory';
import { from, Observable } from 'rxjs';
import { map,catchError } from 'rxjs/operators';


interface IAccount {
    id:  number,
    check_device_url: string,
    check_hostfile_url: string,
    is_active: Boolean,
    name: string,
    need_device_check: Boolean,
    need_hostfile_url: Boolean,
    has_securecx: Boolean,
    check_sites_devices: Boolean
}

@Injectable()
export class AccountsService {
    private apiURL = environment.apiURL;

    private accountName : Array<IAccount> = [];
    private paginate : IPaginate = PaginateFactory.init();
  
  
    public getPaginate(){
      return this.paginate;
    }
    constructor (
        private http : HttpClient,
        private auth : AuthService
    ){}


    /*
    * Execute api call for retrieving search option
    */
   searchAccounts(pageNo, search, isActive, mediaCheck, secureCX){
    const self = this;
    search = search.trim();
    const params = {page : pageNo, search : search, isActive : isActive, mediaCheck : mediaCheck, secureCX : secureCX };
    const secureHeader = this.auth.getTokenHeader();

    return this.http.get(`${this.apiURL}/maintenance/accounts/query`,{params : params, headers : secureHeader }).pipe(
      map( response => {
        self.accountName = response['data'] ;
        self.paginate = PaginateFactory.parse( response );
        return self.accountName as Array<IAccount> ;
      }),
      catchError( error => Observable.throw(error))
    )
 }


   /*
    * Get the list of accounts
    *
    */
    query( pageNo ){
        const secureHeader = this.auth.getTokenHeader();
        return this.http.get(`${this.apiURL}/maintenance/accounts/query?page=${pageNo}`, { 
            headers : secureHeader,
        });
    }

    /*
    * Create a new record for account
    *
    */
    add( args ){
        const secureHeader = this.auth.getTokenHeader();
        return this.http.post(`${this.apiURL}/maintenance/accounts`, args, { 
            headers : secureHeader,
        }).toPromise();
    }

   /*
    * Update existing account
    *
    */
    update(args, id){
        const secureHeader = this.auth.getTokenHeader();
        return this.http.post(`${this.apiURL}/maintenance/accounts/${id}`, args, { 
            headers : secureHeader,
        }).toPromise();
    }

    /*
    * Delete the selected account
    *
    */
    delete( id ){
        const secureHeader = this.auth.getTokenHeader();
        return this.http.delete(`${this.apiURL}/maintenance/accounts/${id}`, { 
            headers : secureHeader,
        }).toPromise();
    }

   /*
    * Get the hostfile for the account
    *
    */
   getHostfile( account ){
      const secureHeader = this.auth.getTokenHeader();
      return this.http.get(`${this.apiURL}/workstation/hostfile/${account}?saved=1`, { 
          responseType : 'text',
          headers : secureHeader,
      });
   }

   /*
    * Updates the hostfile for the account
    *
    */
   updateHostfile(account , details ){
    const secureHeader = this.auth.getTokenHeader();
    return this.http.post(`${this.apiURL}/workstation/hostfile/${account}`, details, { 
        headers : secureHeader,
    }).toPromise();
   }

   
}