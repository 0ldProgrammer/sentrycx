import { Component, OnInit } from '@angular/core';
import * as _ from 'lodash';
import { HistoricalService, WorkstationService, SocketService, NotificationService } from '@app/services';
import { IPaginate, IAgent } from '@app/interfaces';
import {FormControl} from '@angular/forms';

interface IHeader {
  field : string,
  name : string,
  class : string 
}

@Component({
  selector : 'audit-logs-page',
  templateUrl : './audit-logs.page.html',
  styleUrls : ['./audit-logs.page.css']
})
export class AuditLogsPage implements OnInit {
  searchBar = new FormControl('');
	search_value = '';
  public paginate : IPaginate;
  public auditLogs : Array<IAgent> = [];
  public mtrTooltipTitle : String ='MTR Result : ';
  public mtrTooltipContent : String = '<br />';

  public isLoading : Boolean = false;

  public hasFiltered : Boolean = false;
  public filters : any = {};
  public filterOptions : any = {
    fields  : [
      { key : 'user', name : 'User', type : 'input' },
      { key : 'affected_agent', name : 'Agent Name / Account', type : 'input'},
      { key : 'event', name : 'Event', type : 'input'},
      { key : 'workstation_number', name : 'Workstation Name', type : 'input'},
      { key : 'worker_id', name : 'Worker ID', type : 'input'},
      { key : 'startDate', name : 'Start Date', type : 'date'},
      { key : 'endDate', name : 'End Date', type : 'date'}
    ],

    filters : {}

  }

  public auditLogsHeader : Array<IHeader>  = [
    { field : 'user', name : 'User' , class : 'text-left' },
    { field : 'event', name : 'Event' , class : 'text-left' },
    { field : 'workstation_number', name : 'Workstation Name' , class : 'text-left' },
    { field : 'affected_agent', name : 'Affected Agent / Account' , class : 'text-left' },
    { field : 'worker_id', name : 'Worker ID' , class : 'text-left' },
    { field : 'date_triggered', name : 'Date Triggered' , class : 'text-left' },
  ];

  public sortedField = null;

  /*
   * Handles the sorting of reported issues
   */
  public sortHeader( event ) {
    this.sortedField = event['field'];

    this.historicalService.setSort( event['field'], event['direction'] );

    this.loadData();
  }

  /*
   * Constrcutor dependencies
   */
  constructor(
    private historicalService : HistoricalService,
    private service : WorkstationService,
    private socket  : SocketService,
    private notification : NotificationService
  ){}


  /*
   * Executes on loading of page
   */
  ngOnInit(){
    const self = this;

    this.paginate = this.historicalService.getPaginate();

    self.loadData();

    this.historicalService.getAuditLogsConnectionFilters().subscribe( (filterOptions) => {
      self.filterOptions.filters = filterOptions;
    });

    this._listenForNewConnections();
  }

  public searchValue()
	{
  
    const self = this;
    this.isLoading = true;
    this.paginate.currentPage = 1;
    this.historicalService.getAuditLogs( this.paginate.currentPage, this.paginate.perPage, this.filters, this.searchBar.value ).subscribe( (data) => {
      self.auditLogs = data;
      self.paginate = self.historicalService.getPaginate();
      self.isLoading = false;
    });

	}


  /*
   * Connect with socket to check if
   * there are new agent connections
   */
  private _listenForNewConnections(){
    const self = this;

    this.socket.connect();
    this.socket.bind('dashboard:agent-list', () => {
        self.loadData();
    });
  }

  /*
   * Loads the audit logs by page number
   */
    public updateData( pageDetails ){
      this.paginate.currentPage = pageDetails.pageIndex + 1;
      this.paginate.perPage = pageDetails.pageSize;
  
      this.loadData();
    }

  /*
   * Loads the list of audit logs
   */
  public loadData(){
    const self = this;

    this.isLoading = true;

    this.historicalService.getAuditLogs( this.paginate.currentPage, this.paginate.perPage, this.filters, this.searchBar.value ).subscribe( (data) => {
      self.auditLogs = data;
      self.paginate = self.historicalService.getPaginate();
      self.isLoading = false;
    });
    
  }

  /*
   * Handles the search function
   */
  public search( data  ){
    let utcDate;
    let utcTime;
    let utcDateTime;
    const self = this;
    const params = data.params;

    Object.keys( params ).forEach( (val, index ) => {
      if( !params[val] ) return;

      const filterKeyValue = [];
      
      if (val == 'startDate' || val == 'endDate') {
        utcDate = `${params[val].getUTCFullYear().toString().padStart(4, '0')}-${
          (params[val].getUTCMonth()+1).toString().padStart(2, '0')}-${
            params[val].getUTCDate().toString().padStart(2, '0')}`;
        
        utcTime = `${this.padTo2Digits(params[val].getUTCHours().toString())}:${
          (this.padTo2Digits(params[val].getUTCMinutes()+1).toString())}:${
            this.padTo2Digits(params[val].getUTCSeconds().toString())}`;

        utcDateTime = utcDate + ' ' + utcTime;

        filterKeyValue[`${val}[]`] = utcDateTime;

      } else {
        filterKeyValue[`${val}[]`] = params[val];
      }

      self.filters = Object.assign( self.filters, filterKeyValue );
      
    });

    this.loadData();
    self.notification.success('Audit Logs has been filtered.');
    self.hasFiltered = true;
  }

  private padTo2Digits(num) {
    return num.toString().padStart(2, '0');
  }

  /*
   * Handles resetting the search
   */
  public reset(){
    // this.router.navigate(['/connected-agents'] )
    this.hasFiltered = false;
    this.filters = {};
    this.loadData();
  }

  public searchEmptyValue()
  {
      const self = this;
      if(this.searchBar.value=="")
      {
          this.loadData();
      }
      
  }

  public emptySearch()
  {
      this.searchBar.setValue('');
      this.loadData();
  }

  public onSearchIcon() {
    this.paginate.currentPage = 1;
    this.loadData();
  }

}
