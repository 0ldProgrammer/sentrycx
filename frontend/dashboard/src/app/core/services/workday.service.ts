import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AuthService } from './auth.services';
import { environment } from '@env/environment';
import { PaginateFactory } from '@app/factory';
import { IInvalidUsername, IPaginate } from '@app/interfaces';
import { map } from 'rxjs/operators';

@Injectable()
export class WorkdayService {

    private _sortBy = null;
    private apiURL = environment.apiURL;
    private invalidUsernames : Array<any>;
    private paginate : IPaginate = PaginateFactory.init();

    public setSort( field, direction ){
      this._sortBy = { sortBy : field, sortOrder : direction };
    }

    public getPaginate(){
        return this.paginate;
    }

    constructor (
      private http : HttpClient,
      private auth : AuthService ){}

    getProfile( workerID ){
        return this.http.get(`${this.apiURL}/workday/profile/${workerID}`)
      }

    getInvalidUsernames(pageNo, perPage) {
      const self = this;
        const pageParams = { page : pageNo, perPage : perPage };
        const secureHeader = this.auth.getTokenHeader();
    
        let params = Object.assign({}, pageParams );
        if( this._sortBy )
          params = Object.assign( params, this._sortBy );
    
        return this.http.get(`${this.apiURL}/records/invalid-usernames`, { params : params, headers : secureHeader }).pipe(
          map( response => {
            self.invalidUsernames = response['data'];
            self.paginate = PaginateFactory.parse(response );
            return self.invalidUsernames as Array<IInvalidUsername>;
          })
        );
    }

    searchInvalidUsernames(search, pageNo){
      const self = this;
      search = search.trim();
      const pageParams = {search : search, page : pageNo };
      const secureHeader = this.auth.getTokenHeader();
  
      let params = Object.assign({}, pageParams );
      if( this._sortBy )
        params = Object.assign( params, this._sortBy );
  
        return this.http.get(`${this.apiURL}/records/search-invalid-usernames`,{params : params, headers : secureHeader }).pipe(
        map( response => {
          self.invalidUsernames = response['data'];
          self.paginate = PaginateFactory.parse(response );
          return self.invalidUsernames as Array<IInvalidUsername>;
        })
      );
    }
}