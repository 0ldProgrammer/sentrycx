import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '@env/environment';
import { from, Observable } from 'rxjs';
import { map,catchError } from 'rxjs/operators';
import { IAccount, IPaginate, IDesktopApplication } from '@app/interfaces';
import { PaginateFactory } from '@app/factory';
import { ICode } from '../interfaces/code';
import { IAux } from '../interfaces/aux-list';
import { ISubnet, IVlan, IApplicationsList, IApplicationUrls, ISoftwareUpdates, IMailNotifications, ITimeFrames } from '@app/interfaces/index';
import { AuthService } from '@app/services';


@Injectable()
export class MaintenanceService {
    private dataAccount : Array<IAccount> = [];
    private data : Array<ICode> = [];
    private auxData : Array<IAux> = [];
    private subnetData : Array<ISubnet> = [];
    private desktopApplication : Array<IDesktopApplication> = [];
    private vlanData : Array<IVlan> = [];
    private applicationsData : Array<IApplicationsList> = [];
    private softwareUpdatesList : Array<ISoftwareUpdates> = [];
    private paginate : IPaginate = PaginateFactory.init();
    private applicationUrls : Array<IApplicationUrls> = [];
    private mailNotificationsList : Array<IMailNotifications> = [];
    private timeFramesList : Array<ITimeFrames> = [];
  
    public getData(){
      return this.data;
    }
  
    public getPaginate(){
      return this.paginate;
    }
  
    private apiURL = environment.apiURL;
  
    constructor (
      private http : HttpClient,
      private auth : AuthService
    ){}
  
    /*
     * Execute api call for retrieving account data
     *
     */
    getAccounts() {
      const self = this;
      const secureHeader = this.auth.getTokenHeader();

      return this.http.get(`${this.apiURL}/maintenance/accounts`, { headers : secureHeader }).pipe(
        map( response => {
          self.dataAccount = response as Array<IAccount>;
          return self.dataAccount as Array<IAccount>;
        }),
        catchError( error => Observable.throw(error) )
      );
    }
    /*
     * Execute api call for retrieving option list or codes
     *
     */
    getCodes( pageNo, account, search ) {
      const self = this;
      search = search.trim();
      const params = { page : pageNo, account: account, search: search };
      const secureHeader = this.auth.getTokenHeader();

      
      return this.http.get(`${this.apiURL}/maintenance/codes`, { params : params, headers : secureHeader }).pipe(
        map( response => {
          self.data = response['data'] ;
          self.paginate = PaginateFactory.parse( response );
          return self.data as Array<ICode>;
        }),
        catchError( error => Observable.throw(error) )
      );
    }

    getAux( pageNo, account, search ) {
      const self = this;
      search = search.trim();
      const params = { page : pageNo, account: account, search: search };
      const secureHeader = this.auth.getTokenHeader();

      
      return this.http.get(`${this.apiURL}/maintenance/aux`, { params : params, headers : secureHeader }).pipe(
        map( response => {
          self.auxData = response['data'] ;
          self.paginate = PaginateFactory.parse( response );
          return self.auxData as Array<IAux>;
        }),
        catchError( error => Observable.throw(error) )
      );
    }

    /*
     * Execute api call for Assigning or removing codes per account
     *
     */
    codeAssignDeleteAssignment( params ){
      return this.http.post(`${this.apiURL}/maintenance/accounts/codes-assignment`, params ).toPromise();
    }
    /*
     * Execute api call for Assigning or removing codes per account
     *
     */
    auxAssignDeleteAssignment( params ){
      return this.http.post(`${this.apiURL}/maintenance/aux/accounts/assignment`, params ).toPromise();
    }
    /*
     * Execute api call for removing codes
     *
     */
    deleteCode( params ){
      return this.http.post(`${this.apiURL}/maintenance/accounts/delete-code`, params ).toPromise();
    }
    /*
     * Execute api call for removing aux
     *
     */
    deleteAux( params ){
      return this.http.post(`${this.apiURL}/maintenance/aux/accounts/delete`, params ).toPromise();
    }
    /*
     * Execute api call for adding codes
     *
     */
    addOptionList( params ){
      return this.http.post(`${this.apiURL}/maintenance/accounts/add-code`, params ).toPromise();
    }

    addAuxList( params ){
      return this.http.post(`${this.apiURL}/maintenance/aux/add-aux`, params ).toPromise();
    }

    addMapping( params )
    {
       const self = this;
       const secureHeader = this.auth.getTokenHeader();

       return this.http.post(`${this.apiURL}/maintenance/add-mapping`,params).toPromise();
    }

    updateMapping( data, id )
    {
       const self = this;
       const params = {data,id:id}
       const secureHeader = this.auth.getTokenHeader();

       return this.http.post(`${this.apiURL}/maintenance/edit-mapping`,params).toPromise();
    }

    addApplication( data )
    {
       const self = this;
       const params = {data};
       const secureHeader = this.auth.getTokenHeader();

       return this.http.post(`${this.apiURL}/maintenance/add-application`,params).toPromise();
    }

    updateApplication( data, id )
    {
       const self = this;
       const params = {data,id:id}
       const secureHeader = this.auth.getTokenHeader();

       return this.http.post(`${this.apiURL}/maintenance/edit-application`,params).toPromise();
    }

    deleteApplication(id)
    {
      const params = {id:id};
      return this.http.post(`${this.apiURL}/maintenance/delete-application`, params ).toPromise();
    }

    deleteMapping(id)
    {
      const params = {id:id};
      return this.http.post(`${this.apiURL}/maintenance/delete-mapping`, params ).toPromise();
    }

    getMappingList( pageNo,search ) {
      const self = this;
      search = search.trim();
      const params = { page : pageNo,search: search };
      const secureHeader = this.auth.getTokenHeader();

      
      return this.http.get(`${this.apiURL}/maintenance/subnet-mapping`, { params : params, headers : secureHeader }).pipe(
        map( response => {
          self.subnetData = response['data'] ;
          console.log(response);
          self.paginate = PaginateFactory.parse( response );
          return self.subnetData as Array<ISubnet>;
        }),
        catchError( error => Observable.throw(error) )
      );
    }

    getApplicationsList( pageNo,search ) {
      const self = this;
      search = search.trim();
      const params = { page : pageNo,search: search };
      const secureHeader = this.auth.getTokenHeader();

      
      return this.http.get(`${this.apiURL}/maintenance/applications-list`, { params : params, headers : secureHeader }).pipe(
        map( response => {
          self.applicationsData = response['data'] ;
          self.paginate = PaginateFactory.parse( response );
          return self.applicationsData as Array<IApplicationsList>;
        }),
        catchError( error => Observable.throw(error) )
      );
    }

    getVlanMappingList( pageNo,search ) {
      const self = this;
      search = search.trim();
      const params = { page : pageNo,search: search };
      const secureHeader = this.auth.getTokenHeader();

      
      return this.http.get(`${this.apiURL}/maintenance/vlan-mapping`, { params : params, headers : secureHeader }).pipe(
        map( response => {
          self.vlanData = response['data'] ;
          console.log(response);
          self.paginate = PaginateFactory.parse( response );
          return self.vlanData as Array<IVlan>;
        }),
        catchError( error => Observable.throw(error) )
      );
    }


    addVlanMapping( params )
    {
       const self = this;
       const secureHeader = this.auth.getTokenHeader();

       return this.http.post(`${this.apiURL}/maintenance/vlan/add-mapping`,params).toPromise();
    }
    
    updateVlanMapping( data, id )
    {
       const self = this;
       const params = {data,id:id}
       const secureHeader = this.auth.getTokenHeader();

       return this.http.post(`${this.apiURL}/maintenance/vlan/edit-mapping`,params).toPromise();
    }

    deleteVlanMapping(id)
    {
      const params = {id:id};
      return this.http.post(`${this.apiURL}/maintenance/vlan/delete-mapping`, params ).toPromise();
    }

    getClientLocationList()
    {
      const secureHeader = this.auth.getTokenHeader();
      return this.http.get(`${this.apiURL}/maintenance/client-location-list`, { headers : secureHeader });
    }

    getDesktopApplications(search) {

      const pageParams = { search : search };
      let params = Object.assign({}, pageParams );

      const self = this;
      const secureHeader = this.auth.getTokenHeader();
      return this.http.get(`${this.apiURL}/maintenance/deployment-applications-list`, { params : params, headers : secureHeader }).pipe(
        map( response => {
          self.desktopApplication = response['data'] ;
          self.paginate = PaginateFactory.parse( response );
          return self.desktopApplication as Array<IDesktopApplication>;
        }),
        catchError( error => Observable.throw(error) )
      );
    }

    getAccountList()
    {
      const secureHeader = this.auth.getTokenHeader();
      return this.http.get(`${this.apiURL}/maintenance/account-list`, { headers : secureHeader });
    }

    getApplicationUrls( pageNo,search)
    {
      const secureHeader = this.auth.getTokenHeader();
      const params = { page : pageNo,search: search };
      const self = this;

      return this.http.get(`${this.apiURL}/maintenance/applications`, { params : params, headers : secureHeader }).pipe(
        map( response => {
          self.applicationUrls = response['data'] ;
          self.paginate = PaginateFactory.parse( response );
          return self.applicationUrls as Array<IApplicationUrls>;
        }),
        catchError( error => Observable.throw(error) )
      );
    }

    addEditApplicationData(params, fileUpload) {
      let formData = new FormData();
      formData.set('arguments', params['arguments']);
      formData.set('description', params['description']);
       
      if (typeof params['execution_date'] == 'object') {
        const utcDate = `${params['execution_date'].getFullYear().toString().padStart(4, '0')}-${
          (params['execution_date'].getMonth()+1).toString().padStart(2, '0')}-${
            params['execution_date'].getDate().toString().padStart(2, '0')}`;

        formData.set('execution_date', utcDate);
      } else {
        formData.set('execution_date', params['execution_date']);
      }
      
      formData.set('id', params['id']);
      formData.set('is_ps', params['is_ps'] == true || params['is_ps'] == 1 ? '1' : '0');
      formData.set('is_self_install', params['is_self_install'] == true || params['is_self_install'] == 1 ? '1' : '0');
      formData.set('name', params['name']);
      formData.set('ps_or_dl', params['ps_or_dl'] ? params['is_ps'] : '');
      // formData.set('time', params['time']);
      
      // append new file to upload
      if (params['fileUpload'] && fileUpload) {
        formData.append('fileKey', fileUpload, fileUpload.name);
      } else {
        // for current or empty file data 
        formData.set('fileUpload', '');
      }
      
      return this.http.post(`${this.apiURL}/maintenance/application/add-deployment-application`,  formData, {
        reportProgress: true,
        observe: "events"
      }).toPromise();
    }
    
    addApplicationUrl( params ){
      return this.http.post(`${this.apiURL}/maintenance/applications/add`, params ).toPromise();
    }

    getSoftwareUpdatesList(  pageNo,search ){

      const secureHeader = this.auth.getTokenHeader();
      const params = { page : pageNo,search: search };
      const self = this;
      
      return this.http.get(`${this.apiURL}/maintenance/software-updates`, { params : params, headers : secureHeader }).pipe(
        map( response => {
          self.softwareUpdatesList = response['data'] ;
          self.paginate = PaginateFactory.parse( response );
          return self.softwareUpdatesList as Array<ISoftwareUpdates>;
        }),
        catchError( error => Observable.throw(error) )
      );
    }

    getMailNotificationsList(  pageNo,search ){

      const secureHeader = this.auth.getTokenHeader();
      const params = { page : pageNo,search: search };
      const self = this;
      
      return this.http.get(`${this.apiURL}/maintenance/mail-notifications`, { params : params, headers : secureHeader }).pipe(
        map( response => {
          self.mailNotificationsList = response['data'] ;
          self.paginate = PaginateFactory.parse( response );
          return self.mailNotificationsList as Array<IMailNotifications>;
        }),
        catchError( error => Observable.throw(error) )
      );
    }

    getTimeFramesList(  pageNo,search ){

      const secureHeader = this.auth.getTokenHeader();
      const params = { page : pageNo,search: search};
      const self = this;

      return this.http.get(`${this.apiURL}/maintenance/time-frames`, { params : params, headers : secureHeader }).pipe(
        map( response => {
          self.timeFramesList = response['data'];
          self.paginate = PaginateFactory.parse( response );
          return self.timeFramesList as Array<ITimeFrames>;
        }),
        catchError( error => Observable.throw(error) )
      );
    }

    addTimeFrame(data) {
      const self = this;
      const params = { data };
      const secureHeader = this.auth.getTokenHeader();

      return this.http.post(`${this.apiURL}/maintenance/time-frames/add-time-frame`, params).toPromise();
    }

    updateTimeFrame(data, id) {
      const self = this;
      const params = { data, id: id }
      const secureHeader = this.auth.getTokenHeader();

      return this.http.post(`${this.apiURL}/maintenance/time-frames/edit-time-frame`, params).toPromise();
    }

    deleteTimeFrame(id) {
      const params = { id: id };
      return this.http.post(`${this.apiURL}/maintenance/time-frames/delete-time-frame`, params).toPromise();
    }

    executeSoftwareUpdate(data){
      const secureHeader = this.auth.getTokenHeader();
      let params = Object.assign({}, data);
      return this.http.get(`${this.apiURL}/maintenance/software-updates/execute`, { params : params, headers : secureHeader });
    }

    updateMailNotifications(data){
      const secureHeader = this.auth.getTokenHeader();
      return this.http.post(`${this.apiURL}/maintenance/update-mail-notifications`, { data, headers : secureHeader });
    }

    deleteDeploymentApplication( params ){
      return this.http.post(`${this.apiURL}/maintenance/application/delete-deployment-application`, params ).toPromise();
    }
}