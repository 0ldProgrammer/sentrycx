import { Component, OnInit } from '@angular/core';
import * as _ from 'lodash';
import { 
  WorkstationService, 
  SocketService,
  EventLogsService,
  NotificationService,
  CacheService ,
  UserConfigService
} from '@app/services';
import { IPaginate, IAgent } from '@app/interfaces';
import { DomSanitizer } from '@angular/platform-browser';
import { MatDialog } from '@angular/material/dialog';
import {FormControl} from '@angular/forms';
import swal from 'sweetalert2';

interface IHeader {
  field : string,
  name : string,
  class : string 
}

@Component({
  selector : 'desktop-page',
  templateUrl : './desktop.page.html',
  styleUrls : ['./desktop.page.css']
})
export class DesktopPage implements OnInit {
  public clearCache : Boolean = false;
  public skipCache : Boolean = false;
  public paginate : IPaginate;
  public connectedAgents : Array<IAgent> = [];
  public mtrTooltipTitle : String ='MTR Result : ';
  public mtrTooltipContent : String = '<br />';
  public updateCounter : number = 0;
  public isLoading : Boolean = false;


	searchBar = new FormControl('');
	search_value = '';

  public hasFiltered : Boolean = false;
  public filters : any = {};
  public filterOptions : any = {
    fields  : [
      { key : 'agent_name', name : 'Agent Name', type : 'input' },
      { key : 'account', name : 'Account' , type : 'select'},
      { key : 'location', name: 'Location' , type : 'select'},
    ],
    filters : {}

  }

  public flagHeaders : Array<IHeader>  = [
    { field : 'agent_name', name : 'Agent Name' , class : 'text-left' },
    { field : 'job_profile', name : 'Position' , class : 'text-left' },
    { field : 'connection_type', name : 'Connection' , class : 'text-left' },
    { field : 'agent', name : 'AGENT' , class : 'text-left' },
		{ field: 'station_number', name: 'Workstation', class: 'text-left' },
    { field : 'location', name : 'Location', class : 'text-left' },
    { field : 'account', name : 'Account', class : 'text-left' },
    { field : 'mos', name : 'MOS', class : '' },
    { field : 'ram', name : 'Ram', class : 'text-left' },
    { field : 'ram_usage', name : 'Ram Usage %', class : 'text-left' },
    { field : 'disk', name : 'Disk', class : 'text-left' },
    { field : 'free_disk', name : 'Free Disk %', class : 'text-left' },
    { field : 'cpu', name : 'CPU', class : 'text-left' },
    { field : 'cpu_util', name : 'CPU UTIL %', class : 'text-left' },
    { field : 'Throughput_percentage', name : 'Throughput', class : 'text-left' },
    { field : 'lob', name : 'LOB', class: 'text-left' },
    { field : 'programme_msa', name : 'Programme MSA', class : 'text-left'},
    { field : 'supervisor_email_id', name : 'Supervisor Email', class : 'text-left'},
    { field : 'supervisor_full_name', name : 'Supervisor', class: 'text-left' },
    { field : 'updated_at', name : 'Timestamp', class: 'text-left' }
  ];

  public reportType = [
    { value : 'excel', name: 'Export to Excel'},
    { value : 'pdf', name: 'Export to PDF'}
  ];

  public tableColumns = [
    { value : 'agent_name', name : 'Agent Name'  },
    { value : 'job_profile', name : 'Position'  },
    { value : 'connection_type', name : 'Connection'  },
    { value : 'agent', name : 'AGENT'  },
		{ value: 'station_number', name: 'Workstation' },
    { value : 'location', name : 'Location' },
    { value : 'account', name : 'Account' },
    { value : 'mos', name : 'MOS' },
    { value : 'ram', name : 'Ram' },
    { value : 'ram_usage', name : 'Ram Usage %' },
    { value : 'disk', name : 'Disk' },
    { value : 'free_disk', name : 'Free Disk %' },
    { value : 'cpu', name : 'CPU' },
    { value : 'cpu_util', name : 'CPU UTIL %' },
    { value : 'Throughput_percentage', name : 'Throughput' },
    { value : 'media_device', name: 'Media Device'},
    { value : 'lob', name : 'LOB' },
    { value : 'programme_msa', name : 'Programme MSA'},
    { value : 'supervisor_email_id', name : 'Supervisor Email'},
    { value : 'supervisor_full_name', name : 'Supervisor'},
    { value : 'updated_at', name : 'Timestamp'}
  ];


  public selectedColumns : Array<string> = [
    'agent_name', 'connection_type','host_ip_address','location','account'
  ];

  public sortedField = null;

  /*
   * Handles the sorting of reported issues
   */
  public sortFlag( event ) {
    this.sortedField = event['field'];
    this.service.setSort( event['field'], event['direction'] );
    this.clearCache = true;
		this.skipCache = false;

    this.loadData();
  }


  private popupConfig = {
    minWidth : '720px',
    minHeight : '560px'
  }



  public agentStatusTitle = 'Online Agents';

  public agentStatusOptions = [
    { value : 'online', name : 'Online Agents' },
    { value : 'offline', name : 'Offline Agents' },
    { value : 'all', name : 'All Agents'}
  ];

  public onAgentStatusFilter( value ) {
    const selected = _.find(this.agentStatusOptions, { value : value })
    this.agentStatusTitle = selected.name;
    this.filters = Object.assign( this.filters, { connection : value } );
    this.loadData();
    this.notification.success('Connections has been filtered.');
  }


  public speedtestTitle = "<i class='material-icons'>more_horiz</i>";

  public speedtestOptions = [
    { value : 'all', name : 'All speedtest results.'},
    { value : 'threshold', name : 'Low Speedtest'},
  ];

  public speedtestFilter = 'all';

  public onSpeedtestFilter( value ){
    const selected = _.find(this.speedtestOptions, { value : value })

    if( value == this.speedtestFilter ) return;

    this.speedtestFilter = value;

    this.filters = Object.assign( this.filters, { speedtest : value } );

    this.loadData();

    this.notification.success(`Displaying ${selected.name}...`);
  }

  /*
   * Constrcutor dependencies
   */
  constructor(
    private service : WorkstationService,
    private sanitizer : DomSanitizer,
    private socket  : SocketService,
    private eventLog  : EventLogsService,
    private dialog  : MatDialog,
    private notification : NotificationService,
    private cache : CacheService,
    private userConfig : UserConfigService
  ){}


  /*
   * Executes on loading of page
   */
  ngOnInit(){
    const self = this;

    this.paginate = this.service.getPaginate();

    self.loadData();

    this._loadColumnsConfig();

    this.service.getConnectionFilters().subscribe( (filterOptions) => {
      self.filterOptions.filters = filterOptions;
    });

    this._listenForNewConnections();
  }


  /*
   * Dynamic column handler
   */
  public onDynamicColumnChange( selectedColumns ){
    this.selectedColumns = selectedColumns;
    this.userConfig.set('DESKTOP_DASHBOARD_COLUMN_PAGE', selectedColumns );
  }


  /*
   * Reloads the page and prompt a message
   *
   */
  private _loadColumnsConfig(){
    const selectedColumns = this.userConfig.get('DESKTOP_DASHBOARD_COLUMN_PAGE');

    if(!selectedColumns) return;

    this.selectedColumns = selectedColumns;

    console.log(selectedColumns);
  }


  /*
   * Connect with socket to check if
   * there are new agent connections
   */
  private _listenForNewConnections(){
    const self = this;

    this.socket.connect();
    this.socket.bind('dashboard:agent-list', () => {
      self.updateCounter++;
    });
  }

  /*
   * Display the MTR Tooltip
   */
  public updateTooltipContent( agent : IAgent ){
    this.mtrTooltipTitle = `MTR Result : ${agent.agent_name} (${agent.mtr_host})`;
    this.mtrTooltipContent = agent.mtr_result;
  }

  /*
   * Loads the conencted agent by page number
   */
  public updateData( pageDetails ){
    this.paginate.currentPage = pageDetails.pageIndex + 1;
    this.paginate.perPage = pageDetails.pageSize;
    this.skipCache = true;
    this.isLoading = true;
    
    this.loadData();
  }

  public invalidateRefresh(){
    this.paginate.currentPage = 1;
    this.clearCache = true;
    this.skipCache = false;
    this.loadData();
  }

  /*
   * Loads the list of connected agents
   */
  public loadData(){
    const self = this;
    self.updateCounter = 0;
    this.isLoading = true;

    let perPage = this.paginate.perPage ?? 20;
    let currentPage = this.paginate.currentPage ?? 1;

    if( this._loadCache() )
        return;

    self.isLoading = false;
    this.service.getDesktopDashboard( currentPage, perPage, this.filters, this.searchBar.value ).subscribe( (data) => {
      self.connectedAgents = data;
      self.paginate = self.service.getPaginate();
      self.notification.success('Connected devices has been reloaded');

      if( self.skipCache ) return;

      self.cache.save('DESKTOP_DASHBOARD', {
          data : self.connectedAgents,
          paginate : self.paginate
      });
      // self.cache.store('CACHE_DESKTOP_DASHBOARD', {
      //   data : self.connectedAgents,
      //   paginate : self.paginate
      // });
    });
    
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

	public searchValue()
	{
  
		this.isLoading = true;
		this.service.getDesktopDashboard(this.paginate.currentPage, this.paginate.perPage, this.filters, this.searchBar.value).subscribe((data) => {
			this.connectedAgents = data;
			this.paginate = this.service.getPaginate();
			this.isLoading = false;	
		});
	}

  // select type of report
  public setReport( type ){
    let userReportTimezone = localStorage.getItem('USER_TIMEZONE_NAME');
    this.isLoading = true;
    this.service.downloadReportFromDesktopDashboard( type, this.filters, this.selectedColumns, userReportTimezone ).subscribe( (data) => {
      if (data.type == "application/json") {
        swal.fire(
					'Error!',
					'Lost Connection to Server',
					'error'
					);
				this.isLoading = false;
			} else {
        let filename = null;
        let currentDate = new Date();
        let month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
        let date = ("0" + currentDate.getDate()).slice(-2);
        let fullDateFormat =  currentDate.getFullYear() + month + date;
        
        if (type === 'excel') {
          filename = `sentrycx_desktop_${fullDateFormat}.xlsx`;
        } else {
          filename = `sentrycx_desktop_${fullDateFormat}.pdf`;
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
        this.isLoading = false;
      }
    });
  }

  /*
   * Checks if there are data from cache
   * Returns false if no cache
   */
  private _loadCache(){
    const self = this;
    const cacheData = this.cache.get('CACHE_DESKTOP_DASHBOARD');

    if( !cacheData || this.clearCache || self.skipCache ) return false;

    // this.connectedAgents = cacheData['args']['data'];
    // this.paginate = cacheData['args']['paginate'];
    // this.isLoading = false;
    this.cache.countUpdatedData('DESKTOP_DASHBOARD').subscribe( response => {
      self.connectedAgents = cacheData['args']['data'];
      self.paginate = cacheData['args']['paginate'];
      self.isLoading = false;
      self.updateCounter = Math.abs( self.paginate['total'] - response['total'] );
    })

    return true;
  }

  public tooltipMarkup(content){
    return this.sanitizer.bypassSecurityTrustHtml(content);
  }

  /*
   * Handles the search function
   */
  public search( data  ){

    
    const self = this;
    const params = data.params;
    this.clearCache = true;
    // console.log("PARAMS", params)
    // const self = this;

    Object.keys( params ).forEach( (val, index ) => {
      if( !params[val] ) return;

      const filterKeyValue = [];

      filterKeyValue[`${val}[]`] = params[val];

      self.filters = Object.assign( self.filters, filterKeyValue );
    });

    this.loadData();
    self.notification.success('Connections has been filtered.');
    self.hasFiltered = true;
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
  /*
   * Bind events to workstation popup
   */
  private _bindWorkstationPopupEvents( params ){
    const self = this;
    const workerID  = params['workerID'];
    const agentName = params['agentName'];
    const componentInstance = params['componentInstance'];

    // TODO :  Move this into service call to workstation-detilas
    this.service.getAgentWorkstation( workerID ).subscribe( (data) => {
      let hardwareInfo = false;
      if( data['data'].length > 0 )
        hardwareInfo = data['data'][0];

      componentInstance['hardwareInfo'] = hardwareInfo;
      componentInstance['agentName']    = agentName;
      componentInstance['lezap']        = data['lezap'];
      componentInstance['isLoading']    = false;
      componentInstance['eventLogs']    = data['event-logs'];
      componentInstance['singleInfo']   = true;
      componentInstance['remoteSessionLogs'] = data['zoho-logs'];
      componentInstance['mediaDevices'] = data['media-devices'];
      componentInstance['speedtest']    = data['speedtest'];
      
      //self._loadPacDetails( componentInstance, hardwareInfo['pac_address']);
    });

    componentInstance['onEventExtract'].subscribe( (logParams) => {
      self.eventLog.init( params['workerID'], logParams ).then( (response) => {
        componentInstance['eventLogs'].push( response['filename'] );
      });
    });

    componentInstance['onAgentLock'].subscribe( (sessionID) => {
      self.service.lockAgentWorkstation( workerID,  sessionID );
    });

    componentInstance['onAgentWipeout'].subscribe( (workerID) => {
      self.service.wipeoutAgentWorkstation( workerID );
    });

    componentInstance['onMediaStatsRequest'].subscribe( (workerID) => {
      self.service.requestForMediaDeviceStats( workerID );
      // self.notification.success("Device Check Notification has been sent to the agent.");
    });

  }  

  private _loadPacDetails(componentInstance,  url  ){
    const self = this;
    self.service.getPACDetails( url ).subscribe( response =>{
      componentInstance['pacDetails'] = response['data']
    });
    
  }

  onSearchIcon()
  {
    this.isLoading = true;
		this.service.getDesktopDashboard(this.paginate.currentPage, this.paginate.perPage, this.filters, this.searchBar.value).subscribe((data) => {
			this.connectedAgents = data;
			this.paginate = this.service.getPaginate();
			this.isLoading = false;	
		});
  }
}
