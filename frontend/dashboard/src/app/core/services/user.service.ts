import { Injectable, Testability } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { environment } from '@env/environment';
import { PaginateFactory } from '@app/factory';
import { IUser,IPaginate } from '@app/interfaces';
import { map,catchError } from 'rxjs/operators';
import { Observable } from 'rxjs';
import { AuthService } from '@app/services';

interface automatedUsers {
  programme_msa: string;
  tagging: string;
  count_users: string;
}
@Injectable()
export class UserService {
  private data : Array<IUser> = [];
  private paginate : IPaginate = PaginateFactory.init();
  private paginateMSA : IPaginate = PaginateFactory.init();
  private apiURL = environment.apiURL;
  private _sortBy = null;
  private users : Array<any>;
  private records : Array<any>;
  private groups: Array<any>;

  getData(){
    return this.data;
  }

  getPaginate(){
    return this.paginate;
  }

  getMSAData(){
    return this.groups;
  }

  getPaginateMSA(){
    return this.paginateMSA;
  }

  constructor(
    private http : HttpClient,
    private auth : AuthService
  
  ){}

  /*
   * Retrieve the list of Users
   */
  getUsers(pageNo, args){
    const self = this;
    const pageParams = { page : pageNo  };
    const secureheader = this.auth.getTokenHeader();

    let params = Object.assign({}, pageParams,  args );
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    return this.http.get(`${this.apiURL}/maintenance/users`, { params : params, headers : secureheader }).pipe(
      map( response => {

        const parsedData = response['data'].map( item => {
          return item['scope_access']
        });
        self.data = response['data'] ;
        
        self.paginate = PaginateFactory.parse( response );
        return self.data as Array<IUser>;
      }),
      catchError( error => Observable.throw(error) )
    );
  }

  /*
   * Saves a new user
   */
  getUser( userID ){
    return this.http.get(`${this.apiURL}/maintenance/user/${userID}` );
  }

  /*
   * Retreive the list of sites available
   */
  getSites(){
    return this.http.get(`${this.apiURL}/maintenance/users/sites-available` );
  }

  /*
   * Retreive the list of accounts available
   */
  getAccounts(){
    return this.http.get(`${this.apiURL}/maintenance/users/accounts-available` );
  }

  /*
   * Saves a new user
   */
  createUser( params ){
    return this.http.post(`${this.apiURL}/maintenance/user`, params ).toPromise();
  }

  /*
   * Saves an existing user
   */
  updateUser( params, userId ){
    return this.http.post(`${this.apiURL}/maintenance/user/${userId}`, params ).toPromise();
  }

  /*
   * Delete the selected user
   */
  deleteUser( userId ){
    return this.http.delete(`${this.apiURL}/maintenance/user/${userId}`).toPromise();
  }

  searchUsers(search, pageNo, args){

    const self = this;
    search = search.trim();
    const pageParams = {search : search, page : pageNo };
    const secureHeader = this.auth.getTokenHeader();

    let params = Object.assign({}, pageParams,  args );
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

      return this.http.get(`${this.apiURL}/maintenance/search-users`,{params : params, headers : secureHeader }).pipe(
      map( response => {
        self.users = response['data'];
        self.paginate = PaginateFactory.parse(response );
        return self.users as Array<IUser>;
      })
    );
  }

  /*
   * Retrieve the list of filters
   *
   * */
  getFilters( fields : Array<string> ) {
      let params = new HttpParams();
  
      fields.forEach( ( field ) => {
        params = params.append('fields[]', field )
      })
  
      return this.http.get(`${this.apiURL}/users/filters`, { params : params });
  }

  getUsersMSA(){
    const secureHeader = this.auth.getTokenHeader();

    return this.http.get(`${this.apiURL}/maintenance/programme-msa`, { headers : secureHeader });
  }

  searchProgrammeMsa(search, pageNo, args){
    const self = this;
    search = search.trim();
    if(typeof pageNo === 'undefined') {
      pageNo = 1;
    } 
    const pageParams = {search : search, page : pageNo };
    const secureHeader = this.auth.getTokenHeader();

    let params = Object.assign({}, pageParams,  args );
    if( this._sortBy )
    params = Object.assign( params, this._sortBy );

      return this.http.get(`${this.apiURL}/maintenance/search-programme-msa`,{params : pageParams, headers : secureHeader }).pipe(
      map( response => {
        self.groups = response['data'];
        self.paginateMSA = PaginateFactory.parse(response['data']);
        return self.groups as Array<any>;
      })
    );
  }

  /*
   * Saves a new MSA in automated_users
   */
  createMsaUsers( args ){
      const secureHeader = this.auth.getTokenHeader();
      return this.http.post(`${this.apiURL}/maintenance/msa-users`, args, { headers : secureHeader } ).toPromise();
    }

  /*
   * Retrieve the list of MSA from automated_users table
   */

  getMSA(pageNo, args){
    const self = this;

    if(typeof pageNo === 'undefined') {
      pageNo = 1;
    } 

    const pageParams = { page : pageNo  };
    const secureheader = this.auth.getTokenHeader();

    let params = Object.assign({}, pageParams,  args );
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    return this.http.get(`${this.apiURL}/maintenance/msa`, { params : params, headers : secureheader }).pipe(
      map( response => {
        const parsedData = response['data'].map( item => {
          return item['scope_access']
        });
        self.groups = response['data'] ;
        
        self.paginateMSA = PaginateFactory.parse( response );
        return self.groups as Array<automatedUsers>;
      }),
      catchError( error => Observable.throw(error) )
    );
  }

 /*
  * Delete the selected user
  */
  deleteMsa( userId ){
      return this.http.delete(`${this.apiURL}/maintenance/msa/${userId}`).toPromise();
  }
    
}

