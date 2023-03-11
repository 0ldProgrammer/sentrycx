import { Component, OnInit } from '@angular/core';
import { 
    DashboardService, 
    WorkstationService, 
    EventLogsService,
    NotificationService 
} from '@app/services/index';
import { MatDialog } from '@angular/material/dialog';
import { WorkstationDetailsComponent, WebMTRComponent } from '@modules/dashboard/components';
import swal from 'sweetalert2';

@Component({
    selector: 'unresolve-issues-page',
    templateUrl: './unresolve-issues.page.html',
    styleUrls: ['./unresolve-issues.page.css']
})
export class UnresolveIssuesPage implements OnInit {
    public flagPaginate;

    public flagPage: Number = 1;

    public sortedField = null;

    public sortedDirection = null;

    public flags : Array<any> = [];

    public hasFlagFilttered : Boolean = false;

    public filterOptions : any = { locations : [],  accounts : [] , countries : [] };

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

    public flagSelectedColumns : Array<string> = ['code', 'category','location', 'account', 'status_info'] ;

    public flagFilters : any = {};

    public flagColumns = [
        { value : 'host_name', name :'Username' },
        { value : 'code', name :'Code' },
        { value : 'category', name :'Category' },
        { value : 'connection', name: 'Connection' },
        { value : 'agent', name: 'Agent' },
        { value : 'position', name : 'Position'  },
        { value : 'VLAN',   name :'VLAN' }, 
        { value : 'DNS_1',  name :'DNS 1' }, 
        { value : 'DNS_2',  name :'DNS 2' },
        { value : 'subnet', name :'Subnet' },
        { value : 'ISP', name :'ISP'},
        { value : 'location', name: 'Location' },
        { value : 'account', name: 'Account' },
        { value : 'manager', name : 'Manager' },
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
        { value : 'ref_no', name: 'Reference No'},
        { value : 'date_created', name: 'Datetime on issue'},
        { value : 'media_device', name: 'Media Device'},
        { value : 'lob', name : 'LOB' },
        { value : 'programme_msa', name : 'Programme MSA' },
        { value : 'job_profile', name : 'Position' },
        { value : 'supervisor_email_id', name : 'Supervisor Email'},
        { value : 'supervisor_full_name', name : 'Supervisor'}
      ];

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
        { field : 'position', name : 'Position' },
        {field : 'location', name: 'Location'},
        {field : 'account', name: 'Account'},
        { field : 'manager', name : 'Manager' },
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
        {field : 'supervisor_email_id', name : 'Supervisor Email'},
        {field : 'supervisor_full_name', name : 'Supervisor' }
    ]

    /*
    * Dynamic column handler
    */
    public onDynamicColumnChange( selectedColumns ){
    this.flagSelectedColumns = selectedColumns;
    // this.userConfig.set('UNRESOLVED_COLUMNS', selectedColumns );
    }
    constructor(
        private dialog    : MatDialog,
        private service   : DashboardService,
        private workstation  : WorkstationService,
        private eventLog  : EventLogsService,
        private notification : NotificationService
    ) { }

    /*
   * Executes at the start of the page
   *
   */
  ngOnInit() : void {
    this.flagPaginate = this.service.getPaginate();

    this.loadData();

    this._loadFilters();

    // this.socket.connect();
    
    // this._autoReload();

    // this._loadColumnsConfig();
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

  /*
   * Fetch the data via API call then load to frontend dashboard
   *
   */
  public loadData(){
    const self = this;

    this.service.getFlags( this.flagPage, this.flagPaginate.perPage, { old_unresolved : 1 }).subscribe ( (data) => {
      self.flags = data;
      self.flagPaginate = self.service.getPaginate();
    });
  }

    /*
    * Handles the search/filter on Reported Issues Table
    */
    public searchFlag( data : any ) {
        const self = this;
        const params = data.params;
        const selectedColumns = self.flagSelectedColumns;

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
    * Handles the sorting of reported issues
    */
    public sortFlag( event ) {
      this.sortedField = event['field'];
      this.sortedDirection = event['direction'];

      this.service.setSort( event['field'], event['direction'] );

      this.loadFlags();
    }
    
    /*
   * Fetch the list of flags via API then load it to the table
   *
   */
  public loadFlags(){
    const self = this;
    let conditions = this.flagFilters;

    this.flagFilters = conditions;
    conditions['old_unresolved'] = 1;
    

    this.service.getFlags( this.flagPage, this.flagPaginate.perPage, conditions ).subscribe ( (data) => {
      self.flags = data;
      self.flagPaginate = self.service.getPaginate();
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
   * Load the workstation profile of the selected flag
   *
   */
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
    
        self._bindWorkstationPopupEvents( modal.componentInstance, params );
    
        modal.componentInstance['isLoading']    = true;
        
        self._updateWorkstationPopup( params );
    }

    /*
   * Bind events to workstation popup
   */
    private _bindWorkstationPopupEvents( componentInstance, params ){
        const self = this;

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
  
  
        componentInstance['onAgentWipeout'].subscribe( (workerID) => {
          self.workstation.wipeoutAgentWorkstation( workerID );
        });
  
        componentInstance['onMediaStatsRequest'].subscribe ( (workerID) => {
          self.workstation.requestForMediaDeviceStats( workerID );
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

            // this.loadFlags();

            this.service.updateStatusByBatch( conditions , status ).then( (data) => {
                
                self.flagFilters = conditions;
                self.loadFlags();
                // this.loadData();
                self.notification.success(data['msg']);
            });
        });
    }

    public loadWebMTR(agentId) {
      const self = this;
  
      const modal = self.dialog.open(WebMTRComponent, { panelClass: 'custom-full-dialog'});
  
      modal.componentInstance['agentId'] = agentId;
    }
}
