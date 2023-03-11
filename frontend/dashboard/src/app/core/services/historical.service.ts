import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '@env/environment';
import { AuthService } from './auth.services';
import { Observable } from 'rxjs';
import { map,catchError } from 'rxjs/operators';
import { IWorkstation, IAgent, IPaginate } from '@app/interfaces';
import { PaginateFactory } from '@app/factory';
import { UrlSegment } from '@angular/router';

@Injectable()
export class HistoricalService {

    private data : IWorkstation;
    private auditLogs : Array<any>;
    private paginate : IPaginate = PaginateFactory.init();
    private _sortBy = null;

    private apiURL = environment.apiURL;

    public setSort( field, direction ){
      this._sortBy = { sortBy : field, sortOrder : direction };
    }

    public getPaginate(){
        return this.paginate;
    }

    constructor ( 
        private http : HttpClient,
        private auth : AuthService ){}

    getMeanOpinionScore( workerID ){
        const secureHeader = this.auth.getTokenHeader();

        return this.http.get(`${this.apiURL}/records/${workerID}/mean-opinion-score`, { headers : secureHeader });
    }

    // secure cx 
    getSecureCXMonitoring( workerID, url ){
      const secureHeader = this.auth.getTokenHeader();
      const params = { url: url };

      return this.http.get(`${this.apiURL}/records/${workerID}/securecx-monitoring`, { params : params, headers : secureHeader });
    }

    getSecureCXUrls(){
      const secureHeader = this.auth.getTokenHeader();

      return this.http.get(`${this.apiURL}/records/securecx-urls`, { headers : secureHeader });
    }

    getWorkstationProfile( workerID ) {
        const secureHeader = this.auth.getTokenHeader();

        return this.http.get(`${this.apiURL}/records/${workerID}/workstation-profile`, { headers : secureHeader });
    }

    getSpeedtest( workerID ){
        const secureHeader = this.auth.getTokenHeader();

        return this.http.get(`${this.apiURL}/records/${workerID}/speedtest`, { headers : secureHeader });
    };

    getAuditLogs( pageNo, perPage, args, search ){
        
        const self = this;
        const pageParams = { page : pageNo, perPage : perPage, search : search };
        const secureHeader = this.auth.getTokenHeader();
    
        let params = Object.assign({}, pageParams,  args );
        if( this._sortBy )
          params = Object.assign( params, this._sortBy );
    
        return this.http.get(`${this.apiURL}/records/audit`, { params : params, headers : secureHeader }).pipe(
          map( response => {
            self.auditLogs = response['data'];
            self.paginate = PaginateFactory.parse(response );
            return self.auditLogs as Array<IAgent>;
          })
        );
      }

    /*
    * Execute api call for retrieving search option
    */
    searchUsers(search, pageNo, args){

      const self = this;
      search = search.trim();
      const pageParams = {search : search, page : pageNo };
      const secureHeader = this.auth.getTokenHeader();
  
      let params = Object.assign({}, pageParams,  args );
      if( this._sortBy )
        params = Object.assign( params, this._sortBy );
  
        return this.http.get(`${this.apiURL}/records/search-audit-logs`,{params : params, headers : secureHeader }).pipe(
        map( response => {
          self.auditLogs = response['data'];
          self.paginate = PaginateFactory.parse(response );
          return self.auditLogs as Array<IAgent>;
        })
      );
    }

    getAuditLogsConnectionFilters(){
      return this.http.get(`${this.apiURL}/records/audit-logs/filters`);
    }

    getTimestamps(workerID, application) {

      const params = { workerID : workerID, application : application };
      const secureHeader = this.auth.getTokenHeader();
  
      return this.http.get(`${this.apiURL}/records/timestamps`, { params : params, headers : secureHeader });
    }
  
    getPingApplications(workerID) {
      const params = { workerID : workerID };
      const secureHeader = this.auth.getTokenHeader();
  
      return this.http.get(`${this.apiURL}/records/applications`, { params : params, headers : secureHeader });
    }

    pingDownloadReport(workerID, startingDate, endingDate, userTimezone ){
  
      const startDate = { startingDate : startingDate };
      const endDate = { endingDate : endingDate };
      const workerId = { workerID : workerID};
      const timezone = { timezone : userTimezone};

      let params = Object.assign({}, startDate, endDate, workerId, timezone);
      if( this._sortBy )
        params = Object.assign( params, this._sortBy );
  
      // const secureHeader = this.auth.getTokenHeader();
  
      return this.http.get(`${this.apiURL}/records/ping/report`, { params : params, responseType: 'blob'});
    }

    traceDownloadReport(workerID, startingDate, endingDate, userTimezone ){
  
      const startDate = { startingDate : startingDate };
      const endDate = { endingDate : endingDate };
      const workerId = { workerID : workerID};
      const timezone = { timezone : userTimezone};

      let params = Object.assign({}, startDate, endDate, workerId, timezone);
      if( this._sortBy )
        params = Object.assign( params, this._sortBy );
  
      // const secureHeader = this.auth.getTokenHeader();
  
      return this.http.get(`${this.apiURL}/records/trace/report`, { params : params, responseType: 'blob'});
    }

    mosDownloadReport(workerID, startingDate, endingDate, userTimezone ){
  
      const startDate = { startingDate : startingDate };
      const endDate = { endingDate : endingDate };
      const workerId = { workerID : workerID};
      const timezone = { timezone : userTimezone };

      let params = Object.assign({}, startDate, endDate, workerId, timezone);
      if( this._sortBy )
        params = Object.assign( params, this._sortBy );
  
      // const secureHeader = this.auth.getTokenHeader();
  
      return this.http.get(`${this.apiURL}/records/mos/report`, { params : params, responseType: 'blob'});
    }

    speedtestDownloadReport(workerID, startingDate, endingDate, userTimezone ){
  
      const startDate = { startingDate : startingDate };
      const endDate = { endingDate : endingDate };
      const workerId = { workerID : workerID};
      const timezone = { timezone : userTimezone };

      let params = Object.assign({}, startDate, endDate, workerId, timezone);
      if( this._sortBy )
        params = Object.assign( params, this._sortBy );
  
      // const secureHeader = this.auth.getTokenHeader();
  
      return this.http.get(`${this.apiURL}/records/speedtest/report`, { params : params, responseType: 'blob'});
    }

    workstationHistoryDownloadReport(workerID, startingDate, endingDate, userTimezone ){
  
      const startDate = { startingDate : startingDate };
      const endDate = { endingDate : endingDate };
      const workerId = { workerID : workerID};
      const timezone = { timezone : userTimezone };

      let params = Object.assign({}, startDate, endDate, workerId, timezone);
      if( this._sortBy )
        params = Object.assign( params, this._sortBy );
  
      // const secureHeader = this.auth.getTokenHeader();
  
      return this.http.get(`${this.apiURL}/records/workstation-history/report`, { params : params, responseType: 'blob'});
    }
    
    getHistoricalData(workerID, time, date_time){
      const params = {workerID : workerID, time : time, date_time : date_time };
      const secureHeader = this.auth.getTokenHeader();
  
      return this.http.get(`${this.apiURL}/records/one-view-graph`, { params : params, headers : secureHeader });
    }    
}
