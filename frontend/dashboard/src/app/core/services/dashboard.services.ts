import { Injectable } from '@angular/core';
import { HttpClient,HttpParams } from '@angular/common/http';
import { environment } from '@env/environment';
import { Observable } from 'rxjs';
import { map,catchError } from 'rxjs/operators';
import { IFlag, IPaginate } from '@app/interfaces';
import { PaginateFactory } from '@app/factory';
import { AuthService } from './auth.services';

@Injectable()
export class DashboardService {
  private apiURL = environment.apiURL;
  private data : Array<IFlag> = [];
  private paginate : IPaginate = PaginateFactory.init();

  private _sortBy = null;

  public clearSort(){
    this._sortBy = null;
  }

  public setSort( field, direction ){
    this._sortBy = { sortBy : field, sortOrder : direction };
  }

  public getData(){
    return this.data;
  }

  public getPaginate(){
    return this.paginate;
  }

  constructor (
    private http : HttpClient,
    private auth : AuthService
   ){}



  /*
   * Execute api calls for retrieving the overview by account
   *
   */
  getOverview( searchParams ){
    const self = this;

    const secureHeader = this.auth.getTokenHeader();

    let params = new HttpParams();

    if( !searchParams )
      return this.http.get(`${this.apiURL}/flags/overview`, { headers : secureHeader });

    const fields = ['location', 'country', 'account'];

    fields.forEach( field => {
      if( searchParams[field] == null )
        return;
      params = self._groupParams( params, field, searchParams[field] );
      params = params.append('fields[]', field );
      console.log("FOREACH FIELD", field, params );
    });
    
    console.log("PARAMS", params)

    return this.http.get(`${this.apiURL}/flags/overview`, { params : params, headers : secureHeader });
  }

  /*
   * TODO : REDUNDANT SO MUCH REFRACTOR
   * Execute api calls for retrieving the overview by account
   *
   */
  getBreakdown( searchParams ){
    const self = this;

    let params = new HttpParams();

    const secureHeader = this.auth.getTokenHeader();

    if( !searchParams )
      return this.http.get(`${this.apiURL}/flags/breakdown`, { headers : secureHeader });

    const fields = ['location', 'country', 'account'];

    fields.forEach( field => {
      params = self._groupParams( params, field, searchParams[field] );
      params = params.append('fields[]', field );
    });

    return this.http.get(`${this.apiURL}/flags/overview`, { params : params, headers : secureHeader });
  }



  private _groupParams( params, field,  conditions : Array<string> ){
    if( !conditions ) return params;

    conditions.forEach( condition => {
      if( !condition ) return;

      params = params.append(`${field}[]`, condition )
    });

    return params;
  }
  /*
   * Execute api call for retrieving raw data
   *
   */
  getFlags( pageNo , perPage, conditions ) {
    const self = this;

    const secureHeader = this.auth.getTokenHeader();

    let params = { page : pageNo, per_page : perPage  };

    if( conditions )
      params = Object.assign( params, conditions );

    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    return this.http.get(`${this.apiURL}/flags`, { params : params, headers: secureHeader  }).pipe(
      map( response => {
        self.data = response['data'] ;

        self.paginate = PaginateFactory.parse( response );
        return self.data as Array<IFlag>;
      }),
      catchError( error => Observable.throw(error) )
    );
  }

  downloadReportFromSummary( type, args, columns, userTimezone ){

    let arrayObj = {
      arrayColumns : []
    }   

    arrayObj.arrayColumns.push(columns);

    const reportType = { type : type, timezone : userTimezone };
  
    let params = Object.assign({}, reportType, args, arrayObj);
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    const secureHeader = this.auth.getTokenHeader();

    return this.http.get(`${this.apiURL}/flags/report/summary`, { params : params, headers : secureHeader, responseType: 'blob'});
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

    return this.http.get(`${this.apiURL}/flags/filters`, { params : params });
  }

  /*
   * Update status of the request
   *
   */
  updateStatus( id, status ){
    const secureHeader = this.auth.getTokenHeader();

    return this.http.post(`${this.apiURL}/flags/${id}/update-status`, { status : status }, { headers : secureHeader }).toPromise();
  }

  /*
   * Update the status of the multiple flags 
   * depending on the provided criteria/conditions
   */ 
  updateStatusByBatch( conditions, status ){
    const secureHeader = this.auth.getTokenHeader();
    const params = { conditions : conditions, status : status };

    return this.http.post(`${this.apiURL}/flags/batch-update-status`, params, { headers : secureHeader } ).toPromise();
  }
}
