import { Component, OnInit, Sanitizer } from '@angular/core';
import { DashboardService } from '@app/services/dashboard.services';
import { IPaginate } from '@app/interfaces';
import { DomSanitizer} from '@angular/platform-browser';
import { FlagsHelper } from '@app/helpers';
import { environment } from '@env/environment';
import { 
  PlaybookExcelComponent, 
  WorkstationDetailsComponent, 
  ApplicationMonitoringComponent,
  HostfileComponent,
  AgentProfileComponent,
  WebMTRComponent
} from '@modules/dashboard/components';
import { MatDialog } from '@angular/material/dialog';
import * as _ from 'lodash'
import swal from 'sweetalert2';
import { 
  SocketService,
  NotificationService, 
  WorkstationService,
  EventLogsService,
  UserConfigService
} from '@app/services/index';


// TODO : This needs to refractor and break some parts into component 
//        to reduce number of lines
@Component({
  selector: 'dashboard-page',
  templateUrl: './summary.page.html',
  styleUrls: ['./summary.page.css']
})
export class SummaryPage implements OnInit {
  public categoryInfo : any = {
    'application' : { RED : 0, AMBER : 0, YELLOW : 0 },
    'voice' : { RED : 0, AMBER : 0, YELLOW : 0 },
    'network' : { RED : 0, AMBER : 0, YELLOW : 0 }
  };
  public allFlags : Array<any> = [];
  public initialSummary : Array<any> = [];
  public affectedSummary : Array<any> = [];
  public totalSummary : any = null;

  public affectedBreakdown : Array<any> = [];
  public countryBreakdown : Array<any> = [];
  public selectedAccount : String;
  public selectedSeverity : String = null;
  public userTimezone: string = '';

  public cardHeadersClass = {
    accounts : 'card-header-rose',
    flags : 'card-header-rose'
  }

  public blinkIcon;

  private popupConfig = {
    minWidth : '960px',
    minHeight : '850px'
  }

  // TODO : Convert this into interface
  public filterOptions : any = { locations : [],  accounts : [] , countries : [] };


  public hasFlagFilttered : Boolean = false;
  public flagFilterOptions : any = {
    fields  : [
      { key : 'agent_name', name : 'Agent Name', type : 'input' },
      { key : 'code', name : 'Code' , type : 'select'},
      { key : 'category', name : 'Category' , type : 'select'},
      { key : 'VLAN',   name : 'VLAN' , type : 'select'}, 
      { key : 'DNS_1',  name : 'DNS 1' , type : 'select'}, 
      { key : 'DNS_2',  name : 'DNS 2' , type : 'select'},
      { key : 'subnet', name : 'Subnet' , type : 'select'},
      { key : 'ISP', name :'ISP', type : 'select'},
      { key : 'location', name: 'Location' , type : 'select'},
      { key : 'status_info', name : 'Status' , type : 'select'},
    ],
    filters : {}
  }

  public reportType = [
    { value : 'excel', name: 'Export to Excel'},
    { value : 'pdf', name: 'Export to PDF'}
  ];

  public flags : Array<any> = [];
  public flagPaginate : IPaginate;
  public flagFilters : any = {};
  
  public searchParams = null;
  public searchFlagParams = null;

  public filterTag : String = '';
  public flagPage: Number = 1;

  // TODO : Transfer this into summary-list
  public summaryColumnsSelected = ['Logins','Voice', 'Network', 'Application', 'Alerts' ];
  public summaryColumnOptions = ['Logins','Voice', 'Network', 'Application', 'Alerts' ];

  // TODO : Transfer this into issue-list component
  public flagsColumnSelected = ['Category', 'Code','Location', 'Account', 'Status'];
  public flagsColumnOptions  = ['Category', 'Code', 'Location', 'Account', 'Submitted', 'Acknowledged', 'Closed', 'Status' ];


  constructor(
    private service   : DashboardService,
    private sanitizer : DomSanitizer,
    private dialog    : MatDialog,
    private socket    : SocketService,
    private notification : NotificationService,
    private workstation  : WorkstationService,
    private eventLog  : EventLogsService,
    private userConfig : UserConfigService
   ){}


  /*
   * Executes at the start of the page
   *
   */
  ngOnInit() : void {
    this.flagPaginate = this.service.getPaginate();

    this.loadData();

    this._loadFilters();

    this.socket.connect();
    
    this._autoReload();

    this._loadColumnsConfig();

	this._setInitialUserTimezone();
  }

  /* ==================================================================
   *
   * OVERALL SUMMARY PAGE METHODS
   * 
   * ==================================================================*/

  /*
   * Reloads the page and prompt a message
   *
   */
  private _loadColumnsConfig(){
    const selectedColumns = this.userConfig.get('USER_AGENT_COLUMNS');

    if(!selectedColumns) return;

    this.flagSelectedColumns = selectedColumns;
  }

	/*
	 * Set user initial timezone
	 * Monitor every second for user timezone changes
	 */
	private _setInitialUserTimezone() {
		let self = this;
		this.userTimezone = localStorage.getItem('USER_TIMEZONE_NAME');

		setInterval(function () {
			if (self.userTimezone != localStorage.getItem('USER_TIMEZONE_NAME')) {
				self.userTimezone = localStorage.getItem('USER_TIMEZONE_NAME');
				self.loadData();
			}
		}, 1000);
	}

   /*
   * Reloads the page and prompt a message
   *
   */
    private _autoReload(){
      const self = this;
      this.socket.bind('dashboard:summary-list', (data) => {
        self.notification.info("An issue has been raised. Dashboard is being reloaded." )
        // self.search( this.searchParams );
        self.loadData();
      });
    }

  /*
   * Retrieve all the list of filters available for searching
   *
   */
  private _loadFilters(){
    const self   = this;
    const fields = ['location', 'account', 'country', 'status_info' ];

    this.service.getFilters( fields ).subscribe( data  => {
      const filters = data['data'];

      self.filterOptions.countries = filters['country'];
      self.filterOptions.locations = filters['location'];
      self.filterOptions.accounts  = filters['account'];

      self.flagFilterOptions['filters'] = data['issues'];
      self.flagFilterOptions['filters']['code']        = data['codes']['options'];
      self.flagFilterOptions['filters']['location']    = filters['location'];
      self.flagFilterOptions['filters']['category']    = ['voice', 'network', 'application'];
      self.flagFilterOptions['filters']['status_info'] = filters['status_info'];
      self.flagFilterOptions['filters']['agent_name']  = [];
    });
  }

   /*
   * Fetch the data via API call then load to frontend dashboard
   *
   */
  public loadData(){
    const self = this;

    console.log("SEARCH", self.searchParams)
    this.service.getOverview( self.searchParams ).subscribe( (data : Array<any> )=> {
      self.categoryInfo =  FlagsHelper.groupByCategory( data['base'] );
      self.affectedSummary   = FlagsHelper.groupByAccount( data['base'] );
      self.totalSummary = FlagsHelper.groupByCountry( data['total'] )[0];
      self.affectedBreakdown = [];
      const countryBreakdown = data['country'];

      Object.keys(countryBreakdown).forEach( account => {
        self.affectedBreakdown[account] = FlagsHelper.groupByCountry(countryBreakdown[ account ] );
      });


      console.log("TOTAL", self.totalSummary );
      // self.affectedBreakdown = FlagsHelper.groupByCountry( data['country'] );
      
      self.initialSummary  = self.affectedSummary;
      self._analyzeFlagCounts();
    });

    this.service.getFlags( this.flagPage, this.flagPaginate.perPage, { old_unresolved : 0 }).subscribe ( (data) => {
      self.allFlags = data;
      self.flags = data;
      self.flagPaginate = self.service.getPaginate();
    });
  }


  /*
   * Calculate the number of flags to identify the 
   * card header colors
   */
  private _analyzeFlagCounts(){
    const self = this;
    const counter = [
      { class : 'card-header-yellow' },
      { class : 'card-header-warning' } ,
      { class : 'card-header-danger' }
    ];
    self.cardHeadersClass.accounts = 'card-header-rose';
    self.blinkIcon = '';

    const flagColors = FlagsHelper.countFlagsColor( this.affectedSummary );
    
    flagColors.forEach( (value, index) => {
      if( !value ) return;
      
      self.cardHeadersClass.accounts = counter[index]['class'];
      if(self.cardHeadersClass.accounts == 'card-header-yellow') {
        self.blinkIcon = 'slow-blink-icon';
      } else if (self.cardHeadersClass.accounts == 'card-header-warning') {
        self.blinkIcon = 'normal-blink-icon';
      } else {
        self.blinkIcon = 'fast-blink-icon';
      }
    });
  }

  // private _countFlagsByType(type, ){

  // }

  /* ==================================================================
   *
   * ACCOUNT SUMMARY  SECTION  METHODS
   * 
   * ==================================================================*/
  
  /*
   * Handles the search/filter on Summary Table
   */
  public search( params : any ){
    this.searchParams = params;
    this.loadData();
  }


  /*
   * Clears the search/filter on Reported Issues Table
   */
  public reset(){
    this.flagFilters = {};
    this.search( null );
  }

  /* ==================================================================
   *
   * REPORTED ISSUES   SECTION  METHODS
   * 
   * ==================================================================*/
  public flagHeaders = [
    {field : 'host_name', name : 'Username'},
    {field : 'code', name : 'Code'},
    {field : 'category', name : 'Category' },
    {field : 'connection', name: 'Connection'},
    {field : 'agent', name: 'Agent'},
    {field : 'location', name: 'Location'},
    {field : 'account', name: 'Account'},
    {field : 'status_info', name: 'Status'},
    {field : 'mtr_highest_avg', name: 'Highest AVG'},
    {field : 'mtr_highest_loss', name: 'Highest Loss'},
    {field : 'VLAN', name: 'VLAN'},
    {field : 'DNS_1', name: 'DNS 1'},
    {field : 'DNS_2', name: 'DNS 2'},
    {field : 'subnet', name: 'Subnet'},
    {field : 'ISP', name: 'ISP'},
    {field : 'download_speed', name: 'DOWN Speed'},
    {field : 'upload_speed', name: 'Up Speed'},
    {field : 'average_latency', name: 'AVGLAT'},
    {field : 'packet_loss', name: 'APLOSS'},
    {field : 'jitter', name: 'Jitter'},
    {field : 'mos', name: 'MOS'},
    {field : 'timestamp_submitted', name: 'Aging'},
    {field : 'date_created', name: 'Datetime on issue'},
    {field : 'lob', name : 'LOB' },
    {field : 'programme_msa', name : 'Programme MSA' },
    {field : 'job_profile', name : 'Position' },
    {field : 'supervisor_email_id', name : 'Supervisor Email' },
    {field : 'supervisor_full_name', name : 'Supervisor' }
  ]

  public sortedField = null;
  public sortedDirection = null;

  /*
   * Handles the sorting of reported issues
   */
  public sortFlag( event ) {
    this.sortedField = event['field'];
    this.sortedDirection = event['direction'];

    this.service.setSort( event['field'], event['direction'] );

    this.loadFlags();
  }


  public agentStatusOptions = [
    { value : 'online', name : 'Online Agents' },
    { value : 'offline', name : 'Offline Agents' },
    { value : 'all', name : 'All Agents'}
  ]
  public agentStatusTitle = 'Online Agents';
  
  /*
   * Handles the update of agent status filter
   */
  public onAgentStatusFilter( value ) {
    const selected = _.find(this.agentStatusOptions, { value : value })
    this.agentStatusTitle = selected.name;
    this.flagFilters = Object.assign( this.flagFilters, { connection : value } );
    this.loadFlags();
    this.notification.success('Issues has been filtered.');
  }

  public flagSelectedColumns : Array<string> = ['code', 'category','location', 'account', 'status_info'] ;

  public flagColumns = [
    { value : 'ref_no', name: 'Reference No'},
    { value : 'host_name', name :'Username' },
    { value : 'code', name :'Code' },
    { value : 'category', name :'Category' },
    { value : 'connection', name: 'Connection' },
    { value : 'agent', name: 'Agent' },
    { value : 'VLAN',   name :'VLAN' }, 
    { value : 'DNS_1',  name :'DNS 1' }, 
    { value : 'DNS_2',  name :'DNS 2' },
    { value : 'subnet', name :'Subnet' },
    { value : 'ISP', name :'ISP'},
    { value : 'location', name: 'Location' },
    { value : 'account', name: 'Account' },
    { value : 'status_info', name :'Status' },
    { value : 'mtr_highest_avg', name : 'Highest AVG'},
    { value : 'mtr_highest_loss', name : 'Highest Loss'},
    { value : 'download_speed', name: 'DOWN Speed'},
    { value : 'upload_speed', name: 'Up Speed'},
    { value : 'average_latency', name: 'AVGLAT'},
    { value : 'packet_loss', name: 'APLOSS'},
    { value : 'jitter', name: 'Jitter'},
    { value : 'mos', name: 'MOS'},
    { value : 'timestamp_submitted', name: 'Aging'},
    { value : 'date_created', name: 'Datetime on issue'},
    { value : 'media_device', name: 'Media Device'},
    { value : 'lob', name : 'LOB' },
    { value : 'programme_msa', name : 'Programme MSA' },
    { value : 'job_profile', name : 'Position' },
    { value : 'supervisor_email_id', name : 'Supervisor Email' },
    { value : 'supervisor_full_name', name : 'Supervisor'}
    
  ];
  /*
   * Dynamic column handler
   */
  public onDynamicColumnChange( selectedColumns ){
    this.flagSelectedColumns = selectedColumns;
    this.userConfig.set('USER_AGENT_COLUMNS', selectedColumns );
  }


  /* 
   * Load the workstation application monitoring popup
   * of the selected workerID
   *
   */
  public loadAplicationMonitoring(  agent ){
    const self = this;
    const modal = self.dialog.open( ApplicationMonitoringComponent, self.popupConfig );

    modal.componentInstance['agentName'] = agent.agent_name;
    // this.workstation.getApplicationStatus( agent.worker_id , agent.account ).subscribe( (data) => {
    //   modal.componentInstance['agentApplications'] = data['applications'];
    //   modal.componentInstance['automaticApplicationStatus'] = _.filter( data['data'], { type : 'AUTO' } )//data['data'];
    //   modal.componentInstance['manualApplicationStatus']    = _.filter( data['data'], { type : 'MANUAL' } )//data['data'];
    // });
    

    modal.componentInstance['onExtract'].subscribe( (application) => {
      self.workstation.extractApplicationStatistics( agent.worker_id, application );
    });
  }

   
  /*
   * Handles the search/filter on Reported Issues Table
   */
  public searchFlag( data : any ) {
    const self = this;
    const params = data.params;
    const selectedColumns = self.flagSelectedColumns;

    console.log("COLUMNS", self.flagSelectedColumns );
    // self.flagSelectedColumns = Object.assign(self.flagSelectedColumns, data.selected );

    let updatedColumns = selectedColumns.concat( data.selected );
    
    self.flagSelectedColumns = updatedColumns;
    

    Object.keys( params ).forEach( (val, index ) => {
      if( !params[val] ) return;

      const filterKeyValue = [];

      filterKeyValue[`${val}[]`] = params[val];

      self.flagFilters = Object.assign( self.flagFilters, filterKeyValue );
    });

    this.loadFlags();
    self.notification.success('Issues has been filtered.');
    self.hasFlagFilttered = true;
  }

  /*
   * Clears the search/filter on Reported Issues Table
   */
  public clearSearchFlag(){
    const self = this;

    this.flagFilterOptions.fields.forEach( (val, index) =>{
      delete self.flagFilters[`${val['key']}[]` ]
    });
    this.loadFlags();
    self.notification.success('Issues filter has been reset.');
    self.hasFlagFilttered = false;
  }


  public flagColumnChange( selectedColumns ){
    this.flagsColumnSelected = selectedColumns;
  }

  public summaryColumnChange( selectedColumns ){
    this.summaryColumnsSelected = selectedColumns;
  }

  /*
   * Change the Main filters which breakdowns the summary
   * on frontend level
   */
  public changeMainFilter( filter ){
    if( this.selectedSeverity == filter ){
      this.resetToBase();
      return;
    }


    this.selectedSeverity = filter;
    this.filterTag = filter;
    this.affectedSummary = FlagsHelper.filterByTag( this.initialSummary, filter );
    this._analyzeFlagCounts();
  }

  /*
   * Reverts to the base summary list
   *
   */
  public resetToBase(){
    this.selectedSeverity = null;
    this.filterTag = '';
    this.selectedAccount = '';
    this.flagFilters = {};
    this.affectedSummary = this.initialSummary;
    this._analyzeFlagCounts();
    this.clearSearchFlag();
  }

 

  /*
   * Currently static
   */
  public viewPlaybook( accountName ){
    const safeUrl = this.sanitizer.bypassSecurityTrustResourceUrl( environment.excelPath );
    this.dialog.open( PlaybookExcelComponent, {
      width: '1260px',
      data : { account : accountName , url : safeUrl }
    });
  }


  /*
   * Updates the flag filters by account
   *
   */
  public loadFlagsByAccount( account ){
    if( this.selectedAccount == account ){
      this.selectedAccount = null;
      this.resetToBase();
      return;
    }
    this.flagFilters = Object.assign( this.flagFilters, { account : account });

    this.selectedAccount = account;

    this.loadFlags();
  }

  /*
   * Fetch the list of flags via API then load it to the table
   *
   */
  public loadFlags(){
    const self = this;
    // const categoryName = this.filterTag.split(":")[0];
    let conditions = this.flagFilters;

    if( this.filterTag )
      conditions = Object.assign( conditions, {
        category : this.filterTag.split(":")[0].toLocaleLowerCase()
      });

    // if( this.selectedAccount ) {
    //   conditions['account'] = this.selectedAccount
    // }


    this.flagFilters = conditions;
    conditions['old_unresolved'] = 0;
    

    this.service.getFlags( this.flagPage, this.flagPaginate.perPage, conditions ).subscribe ( (data) => {
      self.allFlags = data;
      self.flags = data;
      self.flagPaginate = self.service.getPaginate();
    });
  }

    // select type of report
    public setReport( type ){

      let userReportTimezone = localStorage.getItem('USER_TIMEZONE_NAME');
      
      const unresolved = { old_unresolved : 0 };

      const finalFilter = this.flagFilters && Object.keys(this.flagFilters).length === 0 ? unresolved : this.flagFilters;
    
      this.service.downloadReportFromSummary( type, finalFilter, this.flagSelectedColumns, userReportTimezone ).subscribe( (data) => {
        let filename = null;
        let currentDate = new Date();
        let month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
        let date = ("0" + currentDate.getDate()).slice(-2);
        let fullDateFormat =  currentDate.getFullYear() + month + date;
        
        if (type === 'excel') {
          filename = `sentrycx_summary_${fullDateFormat}.xlsx`;
        } else {
          filename = `sentrycx_summary_${fullDateFormat}.pdf`;
        }
        
        let blob = new Blob([data], {
          type: 'application/octet-stream',
      });
      if (typeof window.navigator.msSaveBlob !== 'undefined') {
        window.navigator.msSaveBlob(blob, filename);
    } else {
        let blobURL = window.URL.createObjectURL(blob);
        let tempLink = document.createElement('a');
        tempLink.style.display = 'none';
        tempLink.href = blobURL;
        tempLink.download = filename;
        tempLink.click();
        window.URL.revokeObjectURL(blobURL);
    }
    });
  }

  /*
   * Update the page number and load the data based
   * on that page number.
   */
  public updatePage( pageDetails ){
    this.flagPage = pageDetails.pageIndex + 1;
    this.flagPaginate.perPage = pageDetails.pageSize;
    this.loadFlags();
  }

  /*
   * Update the status of a flag from Pending to Acknowlege,
   * to Confirm.
   */
  public changeStatus( id, status ){
    const params = { id : id , status : status };
    let self = this;
    this.service.updateStatus(params.id, params.status).then( (data) =>{
      self.loadData();
    });
  }


  /*
   * Update the status of the flags by batch
   */
  public changeStatusByBatch( conditions, status ){
    const self = this;
    const actionRef = {
      Acknowledge : 'Acknowledge',
      'For Confirmation' : 'Resolve'
    };

    swal.fire({
      title: `Confirm batch ${actionRef[status]}?`,
      text: 'You will not be able to revert the status once you proceed.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: `Confirm ${actionRef[status]}`,
      cancelButtonText: 'Cancel',
      customClass:{
        confirmButton: "btn btn-success",
        cancelButton: "btn btn-danger",
      },
        buttonsStyling: false
    }).then( result => {
      if( !result.value) 
        return;

        this.loadFlags();

        this.service.updateStatusByBatch( conditions , status ).then( (data) => {
          // self.flagFilterOptions = conditions;
          this.flagFilters = conditions;
          this.loadFlags();
          self.notification.success(data['msg']);
        });
    })

    

    
  }

  /* 
   * Load the hostfile settings of the account
   *
   */
  public loadHostfile( account ){
    const modal = this.dialog.open( HostfileComponent, { 
      minWidth: '1024px',
      minHeight : '850px'
    });

    modal.componentInstance['account'] = account;
  }


  /* 
   * Load the agent profile of the selected workerID
   *
   */
  public loadAgentProfile( workerID ){
    const self = this;

    const modal = self.dialog.open( AgentProfileComponent, self.popupConfig );

    modal.componentInstance['workerID'] = workerID;
  }

  /* 
   * Load the workstation profile of the selected flag
   *
   */
  // public loadWorkstation( flagID, agentName, workerID ){
  public loadWorkstation( issueDetails ){
    const flagID = issueDetails['id'];
    const agentName = issueDetails['agent_name'];
    const workerID  = issueDetails['worker_id'];
    const self  = this;
    const modal = self.dialog.open( WorkstationDetailsComponent,  {
      minWidth : '720px',
      minHeight : '80vh',
    });

    let params = {
      componentInstance : modal.componentInstance, 
      flagID    : flagID, 
      agentName : agentName, 
      workerID  : workerID,
      pageNo    : 1,
    }

    self._checkIfDone( params );
    self._bindWorkstationPopupEvents( modal.componentInstance, params );

    modal.componentInstance['isLoading']    = true;
    
    self._updateWorkstationPopup( params );
  }

  /*
   * Bind events to workstation popup
   */
    private _bindWorkstationPopupEvents( componentInstance, params ){
      const self = this;
      componentInstance['onDestroy'].subscribe( () => {
        self.socket.unbind('dashboard:agent-workstation-updated');
      });
  
      componentInstance['onUpdate'].subscribe( ( pageNo ) => {
        params['pageNo'] = pageNo;
        componentInstance['isLoading']    = true;
        self._updateWorkstationPopup( params );
      });

      componentInstance['onRequest'].subscribe( (requestParams) => {
        componentInstance['progress'] = 1;
  
        self.workstation.requestWorkstationProfile( componentInstance['hardwareInfo'].redflag_id, requestParams );
      });

      componentInstance['onEventExtract'].subscribe( (logParams) => {
        self.eventLog.init( params['workerID'], logParams ).then( (response) => {
          componentInstance['eventLogs'].push( response['filename'] );
        });
      });

      // componentInstance['onAgentLock'].subscribe( (sessionID) => {
      //   self.workstation.lockAgentWorkstation( params['workerID'],  sessionID ).then( (data ) => {
      //     // self.notification.success( data['msg']);
      //   })
      // });

      componentInstance['onAgentWipeout'].subscribe( (workerID) => {
        self.workstation.wipeoutAgentWorkstation( workerID );
      });

      componentInstance['onMediaStatsRequest'].subscribe ( (workerID) => {
        self.workstation.requestForMediaDeviceStats( workerID );
        // self.notification.success("Device Check Notification has been sent to the agent.");
      });

    }
  /*
   * Listen for socket connectivty
   */
  private _checkIfDone( args ){
    const componentInstance = args['componentInstance'];
    const workerID  = args['workerID'];
    const self      = this;
    const agentName = args['agentName']
    
    this.socket.bind('dashboard:agent-workstation-updated', ( data ) => {
      if( data['worker_id'] != workerID ) return;

      if( data['progress'] == 100 ){
        self.notification.info(`Done extracting updated workstation info. ${agentName}`);
        this._updateWorkstationPopup( args );
        return;
      }
      
      componentInstance['progress'] = data['progress'];
    });
  }

  /*
   * Updates the data on the workstation profile
   */ 
  private _updateWorkstationPopup( args ){
    const componentInstance = args['componentInstance'];
    const flagID    = args['flagID'];
    const agentName = args['agentName']
    const pageNo    = args['pageNo'];
    const self      = this;

    this.workstation.getWorkstationProfile( flagID, pageNo ).subscribe( (data) => {
      componentInstance['hardwareInfo'] = data;
      componentInstance['agentName']    = agentName;
      componentInstance['paginate']     = self.workstation.getPaginate();
      componentInstance['progress']     = self.workstation.getProgress();
      componentInstance['lezap']        = self.workstation.getLezap();
      componentInstance['isLoading']    = false;
      componentInstance['eventLogs']    = self.workstation.getEventLogs();
      componentInstance['remoteSessionLogs'] = self.workstation.getZohoLogs();
      componentInstance['mediaDevices']  = self.workstation.getMediaDevices();
      componentInstance['speedtest']     = self.workstation.getSpeedtest();
    });
  }

  public loadWebMTR(agentId) {
		const self = this;

		const modal = self.dialog.open(WebMTRComponent, { panelClass: 'custom-full-dialog'});

		modal.componentInstance['agentId'] = agentId;
	}

  
}
