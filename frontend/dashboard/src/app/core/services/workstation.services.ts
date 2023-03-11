import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '@env/environment';
import { Observable } from 'rxjs';
import { map,catchError } from 'rxjs/operators';
import { IWorkstation, IAgent, IPaginate } from '@app/interfaces';
import { AuthService,CacheService } from '@app/services';
import { PaginateFactory } from '@app/factory';

interface ILocation {
  location : string
}
@Injectable()
export class WorkstationService {
  // TODO : Add an Interface for this
  private lezap : any;
  private progress : number;
  private data : IWorkstation;
  private location : ILocation;
  private connectedAgents : Array<any>;
  private eventLogs : Array<any>;
  private zohoLogs : Array<any> = [];
  private paginate : IPaginate = PaginateFactory.init();
  private mediaDevices : any = null;
  private speedtest : any = null

  private _sortBy = null;

  public clearSort(){
    this._sortBy = null;
  }

  public setSort( field, direction ){
    this._sortBy = { sortBy : field, sortOrder : direction };
  }

  public getLezap(){
    return this.lezap;
  }

  public getData(){
    return this.data as IWorkstation;
  }

  public getEventLogs(){
    return this.eventLogs;
  }

  public getPaginate(){
    return this.paginate;
  }

  public getProgress(){
    return this.progress;
  }

  public getZohoLogs(){
    return this.zohoLogs;
  }

  public getMediaDevices(){
    return this.mediaDevices;
  }

  public getSpeedtest(){
    return this.speedtest;
  }

  public getUserToken(){
    return this.auth.getToken();
  }

  private apiURL = environment.apiURL;

  constructor (
    private http : HttpClient, 
    private auth : AuthService,
    private cache : CacheService
  ){}

  getReportType()
  {
    return this.http.get(`${this.apiURL}/reports/type`);
  }

  getThreshold(code, account)
  {
    const self = this;
    return this.http.get(`${this.apiURL}/reports/threshold/${code}/${account}`);

  }

  getLocationByAccount(account)
  {
    const self = this;
    return this.http.get(`${this.apiURL}/reports/location/${account}`);
  }

  DownloadReportDetails( params ){
    return this.http.post(`${this.apiURL}/reports/details`, params, {responseType: 'blob'} );
    // return this.http.post(`${this.apiURL}/reports/details/`, params );
  }

  requestWorkstationProfile( flagID, args ){
    const secureHeader = this.auth.getTokenHeader();
    return this.http.post(`${this.apiURL}/flags/${flagID}/workstation-profile/send-request`, args, { headers : secureHeader } ).toPromise();
  }

  getWorkstationProfile(flagID, pageNo ){
    const self = this;
    const params = {
      page : pageNo,
      redflag_id : flagID
    };
    return this.http.get(`${this.apiURL}/flags/${flagID}/workstation-profile`, { params : params }).pipe(
      map( response => {
        self.data      = response['profile']['data'][0];
        self.lezap     = response['lezap'];
        self.progress  = (response['progress'] ) ? response['progress']['progress'] : 0 ;
        self.paginate  = PaginateFactory.parse( response['profile'] );
        self.eventLogs = response['event-logs'];
        self.zohoLogs  = response['zoho-logs'];
        self.mediaDevices = response['media-devices'];
        self.speedtest = response['speedtest'];
        return self.data as IWorkstation;
      }),
      catchError( error => Observable.throw(error) )
    );
  }

  getWorkstationProgress( workerID ){
    return this.http.get(`${this.apiURL}/flags/${workerID}/workstation-profile/progress`)
  }

  sendRequest( params ){
    const secureHeader = this.auth.getTokenHeader();
    return this.http.post(`${this.apiURL}/workstation/agent/mtr-request`, params, { headers : secureHeader } ).toPromise();
  }

  lockAgentWorkstation( workerID, params){
    const secureHeader = this.auth.getTokenHeader();
    return this.http.post(`${this.apiURL}/workstation/${workerID}/lock-screen`, params, { headers : secureHeader } ).toPromise();

  }

  getEventApprovals(pageNo)
  {
    const secureHeader = this.auth.getTokenHeader();
        return this.http.get(`${this.apiURL}/workstation/approvals?page=${pageNo}`, { 
            headers : secureHeader
        });
  }


  searchEventApprovals(search){
    const self = this;
    search = search.trim();
    const params = {search : search };
    const secureHeader = this.auth.getTokenHeader();

    return this.http.get(`${this.apiURL}/workstation/approvals`,{params : params, headers : secureHeader })
  }

  ApproveEvent( approval_id, params ){
    return this.http.post(`${this.apiURL}/workstation/approvals/${approval_id}`, params ).toPromise();
  }

  getMediaSites( workerID ){
    return this.http.get( `${this.apiURL}/workstation/${workerID}/media-device/sites`);
  }

  sendMediaDeviceStats( workerID, params ){
    return this.http.post( `${this.apiURL}/workstation/${workerID}/media-device`, params ).toPromise();
  }

  sendMediaDeviceStatsPerSite( workerID, params ){
    return this.http.post( `${this.apiURL}/workstation/${workerID}/media-device/sites`, params ).toPromise();
  }

  requestForMediaDeviceStats( workerID ){
    return this.http.post( `${this.apiURL}/workstation/${workerID}/media-device/request`, {}).toPromise();
  }

  wipeoutAgentWorkstation( workerID){
    return this.http.post(`${this.apiURL}/workstation/${workerID}/wipeout`, {} ).toPromise();
  }

  getAgentWorkstation( workerID ){
    const params = { worker_id : workerID };
    return this.http.get(`${this.apiURL}/workstation/profile`, { params : params })
  }

  getConnection( connectionID ){
    return this.http.get(`${this.apiURL}/workstation/connected-agents/${connectionID}`);
  }

  getConnectionFilters(){
    return this.http.get(`${this.apiURL}/workstation/connected-agents/filters`);
  }

  getGeoMappingFilters(){
    return this.http.get(`${this.apiURL}/workstation/geo-mapping/filters`)
  }

  getSecurecxFilters(){
    return this.http.get(`${this.apiURL}/workstation/securecx/filters`);
  }

  getReportsTypeOnEmail(){

    let email = location.search.slice(1).split("&")[0].split("=")[1];
    let expires = location.search.slice(2).split("&")[1].split("=")[1];
    let signature = location.search.slice(3).split("&")[2].split("=")[1];

    const params = { email : email, expires : expires, signature : signature };

    return this.http.get(`${this.apiURL}/reports/disable`, {params : params});
  }

  getApplicationStatus(workerID, monitoringID) {
    const params = { monitoringID: monitoringID };
    return this.http.get(`${this.apiURL}/workstation/${workerID}/monitoring`, { params: params });
  }

  extractApplicationStatistics( workerID, application ){
    const params = { application : application }
    return this.http.post(`${this.apiURL}/workstation/${workerID}/monitoring/extract`, params ).toPromise();
  }

  // TODO : Create report.service.ts for thi
  downloadIntervalSummaryReport( params ){
    const secureHeader = this.auth.getTokenHeader();
    return this.http.get(`${this.apiURL}/reports/interval-summary`, { params : params, headers : secureHeader, responseType: 'blob'}); 
  }

  // TODO : Create report.service.ts for thi
  downloadIntervalDetailReport( params ){
    const secureHeader = this.auth.getTokenHeader();
    return this.http.get(`${this.apiURL}/reports/interval-detail`, { params : params, headers : secureHeader, responseType: 'blob'}); 
  }

  downloadReport( type, args, columns, userTimezone ){
    
    let arrayObj = {
      arrayColumns : []
    }  

    arrayObj.arrayColumns.push(columns);

    const reportType = { type : type, timezone : userTimezone };

    let params = Object.assign({}, reportType, args, arrayObj );
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    const secureHeader = this.auth.getTokenHeader();

    return this.http.get(`${this.apiURL}/workstation/report`, { params : params, headers : secureHeader, responseType: 'blob'});
  }

  downloadSecurecxReport( type, args, userTimezone ){

    const reportType = { type : type, timezone : userTimezone };

    let params = Object.assign({}, reportType, args );
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    const secureHeader = this.auth.getTokenHeader();

    return this.http.get(`${this.apiURL}/workstation/securecx-report`, { params : params, headers : secureHeader, responseType: 'blob'});
  }

  downloadReportFromDesktopDashboard( type, args, columns, userTimezone ){

    let arrayObj = {
      arrayColumns : []
    }  

    arrayObj.arrayColumns.push(columns);

    const reportType = { type : type, timezone : userTimezone };

    let params = Object.assign({}, reportType, args, arrayObj );
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    const secureHeader = this.auth.getTokenHeader();

    return this.http.get(`${this.apiURL}/workstation/report/desktop-dashboard`, { params : params, headers : secureHeader, responseType: 'blob'});
  }

  downloadReportFromMOSView( type, search, breakdownField ){

    const reportType = { type : type, breakdownField : breakdownField };
    
    search = search.trim();
    const searchValue = {search : search}; 
    let params = Object.assign({}, reportType, searchValue);
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    const secureHeader = this.auth.getTokenHeader();

    return this.http.get(`${this.apiURL}/workstation/report/mos-view`, { params : params, headers : secureHeader, responseType: 'blob'});
  }

  downloadReportFromApplicationsView( type, appType ){

    const reportType = { type : type };
  
    const applicationsType = { appType : appType }; 
    let params = Object.assign({}, reportType, applicationsType);

    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    const secureHeader = this.auth.getTokenHeader();

    return this.http.get(`${this.apiURL}/workstation/report/applications-view`, { params : params, headers : secureHeader, responseType: 'blob'});
  }

  downloadReportFromConnectedTOC( type, args, columns, userTimezone ){

    let arrayObj = {
      arrayColumns : []
    }  

    arrayObj.arrayColumns.push(columns);

    const reportType = { type : type, timezone : userTimezone };
  
    let params = Object.assign({}, reportType, args, arrayObj);
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    const secureHeader = this.auth.getTokenHeader();

    return this.http.get(`${this.apiURL}/workstation/report/connected-toc`, { params : params, headers : secureHeader, responseType: 'blob'});
  }

  updateHostfile( workerID, params ){
    const secureHeader = this.auth.getTokenHeader();
    
    return this.http.post(`${this.apiURL}/workstation/${workerID}/hostfile`, params, { headers : secureHeader } ).toPromise();
  }

  updateMailNotification( params ){
    const secureHeader = this.auth.getTokenHeader();
    
    return this.http.post(`${this.apiURL}/reports/update-email-notifications`, params, { headers : secureHeader } );
  }

  /*
   * Exceute api call for retrieving the cmd/response
   */
  getWebCMD(workerID){
    const secureHeader = this.auth.getTokenHeader();
    return this.http.get( `${this.apiURL}/workstation/${workerID}/web-command-line`, { headers : secureHeader});
  }

  /*
   * Execute api call for execution of command 
   */
  executeCommand(workerID, params ){
    const secureHeader = this.auth.getTokenHeader();
    return this.http.post( `${this.apiURL}/workstation/${workerID}/web-command-line`, params, { headers : secureHeader}).toPromise();
  } 

  getConnectedStats(search){
    const self = this;
    search = search.trim();
    const params = {search : search };
    const secureHeader = this.auth.getTokenHeader();

    return this.http.get(`${this.apiURL}/workstation/mos-view`, {params : params, headers : secureHeader });
  }

  getAgentsApplications(applicationsType){
    const secureHeader = this.auth.getTokenHeader();

    const params = { applicationsType : applicationsType };

    return this.http.get(`${this.apiURL}/workstation/applications-view`, { params : params, headers : secureHeader });
  }

    /*
    * Execute api call for retrieving search option
    */
  searchAccounts(search){
      const self = this;
      search = search.trim();
      const params = {search : search };
      const secureHeader = this.auth.getTokenHeader();

      return this.http.get(`${this.apiURL}/workstation/search-mos`,{params : params, headers : secureHeader })
  }

  /*
  * Execute api call for retrieving search option
  */
  searchApplicationAccounts(applicationsType, search){
      const self = this;
      search = search.trim();
      const params = {applicationsType : applicationsType, search : search };
      const secureHeader = this.auth.getTokenHeader();

      return this.http.get(`${this.apiURL}/workstation/search-application-accounts`,{params : params, headers : secureHeader })
  }


  getConnectedAgents( pageNo, perPage, args, search ){
    const self = this;
    const pageParams = { page : pageNo, perPage : perPage, search : search };
    const secureHeader = this.auth.getTokenHeader();
  
    let params = Object.assign({}, pageParams,  args );
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    return this.http.get(`${this.apiURL}/workstation/connected-agents`, { params : params, headers : secureHeader }).pipe(
      map( response => {
        self.connectedAgents = response['data'];
        self.paginate = PaginateFactory.parse(response );
        
        return self.connectedAgents as Array<IAgent>;
      })
    );
  }

  getSecurecxAgents( pageNo, perPage, args, search ){
    const self = this;
    const pageParams = { page : pageNo, perPage : perPage, search : search };
    const secureHeader = this.auth.getTokenHeader();

    let params = Object.assign({}, pageParams,  args );
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    return this.http.get(`${this.apiURL}/workstation/securecx`, { params : params, headers : secureHeader }).pipe(
      map( response => {
        self.connectedAgents = response['data'];
        self.paginate = PaginateFactory.parse(response );

        return self.connectedAgents as Array<IAgent>;
      })
    );
  }

  getGeoMappingList( pageNo, perPage, args  ){
    const self = this;
    const pageParams = { page : pageNo, perPage : perPage };
    const secureHeader = this.auth.getTokenHeader();

    let params = Object.assign({}, pageParams,  args );
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    return this.http.get(`${this.apiURL}/workstation/geo-mapping`, { params : params, headers : secureHeader }).pipe(
      map( response => {
        self.paginate = PaginateFactory.parse(response['details'] );

        return response;
      })
    );
  }

  getInactiveAgents( pageNo, perPage, args  ){
    const self = this;
    const pageParams = { page : pageNo, perPage : perPage };
    const secureHeader = this.auth.getTokenHeader();

    let params = Object.assign({}, pageParams,  args );
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    return this.http.get(`${this.apiURL}/workstation/inactive-agents`, { params : params, headers : secureHeader }).pipe(
      map( response => {
        self.connectedAgents = response['data'];
        self.paginate = PaginateFactory.parse(response );

        return self.connectedAgents as Array<IAgent>;
      })
    );
  }

  
  getDesktopDashboard( pageNo, perPage, args, search  ){
    const self = this;
    const pageParams = { page : pageNo, perPage : perPage, search : search };
    const secureHeader = this.auth.getTokenHeader();

    let params = Object.assign({}, pageParams,  args );
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    return this.http.get(`${this.apiURL}/workstation/dashboard`, { params : params, headers : secureHeader }).pipe(
      map( response => {
        self.connectedAgents = response['data'];
        self.paginate = PaginateFactory.parse(response );
        return self.connectedAgents as Array<IAgent>;
      })
    );
  }

  getConnectedEngineers( pageNo, perPage, args  ){
    const self = this;
    const pageParams = { page : pageNo, perPage : perPage };
    const secureHeader = this.auth.getTokenHeader();

    const params = Object.assign({}, pageParams,  args );
    return this.http.get(`${this.apiURL}/workstation/connected-toc`, { params : params, headers : secureHeader }).pipe(
      map( response => {
        self.connectedAgents = response['data'];
        self.paginate = PaginateFactory.parse(response );
        return self.connectedAgents as Array<IAgent>;
      })
    );
  }


  getPACDetails( url ){
    const params = { url : url };

    return this.http.get(`${this.apiURL}/pac/pac.php`, { params : params });
  }

  
  getBreakdownData( selectedFilter, args  ){
    const self = this;
    const filter = { selectedFilter : selectedFilter };
    const secureHeader = this.auth.getTokenHeader();

    let params = Object.assign({}, filter,  args );
    if( this._sortBy )
      params = Object.assign( params, this._sortBy );

    return this.http.get(`${this.apiURL}/workstation/filter-breakdown`, { params : params, headers : secureHeader }).pipe(
      map( response => {
        if (response) {
          self.connectedAgents = response['data'];
          self.paginate = PaginateFactory.parse(response );
          return self.connectedAgents as Array<IAgent>;
        } else {
          self.connectedAgents = [];
          return self.connectedAgents;
        }

      })
    );
  }

  getLocation(lat,long)
  {
    return this.http.get(`https://nominatim.openstreetmap.org/reverse.php?lat=${lat}&lon=${long}&zoom=18&format=jsonv2`);
  }
}
