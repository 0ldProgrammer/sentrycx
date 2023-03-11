import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '@env/environment';
import { Observable } from 'rxjs';
import { map,catchError } from 'rxjs/operators';
import { IFlag, IPaginate } from '@app/interfaces';
import { PaginateFactory } from '@app/factory';

@Injectable()
export class FlagsService {
  private data : Array<IFlag> = [];
  private paginate : IPaginate = PaginateFactory.init();

  public getData(){
    return this.data;
  }

  public getPaginate(){
    return this.paginate;
  }

  private apiURL = environment.apiURL;

  constructor (private http : HttpClient ){}

  /*
   * Execute api call for retrieving raw data
   *
   */
  getFlags( pageNo ) {
    const self = this;

    const params = { page : pageNo };

    return this.http.get(`${this.apiURL}/flags`, { params : params }).pipe(
      map( response => {
        self.data = response['data'] ;
        // self.paginate = response as IPaginate;
        // self.paginate = PagerHelper.parse( response );
        self.paginate = PaginateFactory.parse( response );
        return self.data as Array<IFlag>;
      }),
      catchError( error => Observable.throw(error) )
    );
  }



  /*
   * Update status of the request
   *
   */
  updateStatus( id, status ){
    return this.http.post(`${this.apiURL}/flags/${id}/update-status`, { status : status }).toPromise();
  }
}
