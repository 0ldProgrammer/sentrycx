import { Component, OnInit } from '@angular/core';
import { WorkstationService, NotificationService, UserConfigService,CacheService } from '@app/services';
import { IPaginate, IAgent } from '@app/interfaces';
import * as _ from 'lodash';

interface IHeader {
  field : string,
  name : string,
  class : string 
}

@Component({
    selector: 'inactive-agents-page',
    templateUrl: './inactive-agents.page.html',
    styleUrls: ['./inactive-agents.page.css']
})
export class InactiveAgentsPage implements OnInit {
    public connectedAgents : Array<IAgent> = [];
    public updateCounter : number = 0;
    public isLoading : Boolean = true;
    public paginate  : IPaginate;
    public filters   : any = {};
    public hasFiltered : Boolean = false;
    public sortedField = null;
    public clearCache : boolean = false;
    public skipCache : Boolean = false;
    public speedtestFilter = 'all';

    public selectedColumns : Array<string> = [
        'connection','net_type', 'location',
        'account', 'status_info', 'download_speed', 
        'upload_speed','average_latency', 
        'packet_loss', 'jitter', 'mos', 'updated_at'
    ];

    public tableColumns = [
      { value : 'host_name', name: 'Username' },
      { value : 'connection_type', name: 'Connection' },
      { value : 'net_type', name: 'Agent' },
      { value : 'account', name: 'Account' },
      { value : 'location', name: 'Location' },
      { value : 'mtr_highest_avg', name : 'Highest AVG'},
      { value : 'mtr_highest_loss', name : 'Highest Loss'},
      { value : 'VLAN', name: 'VLAN'},
      { value : 'DNS_1', name: 'DNS 1'},
      { value : 'DNS_2', name: 'DNS 2'},
      { value : 'subnet', name :'Subnet' },
      { value : 'ISP', name :'ISP'},
      { value : 'download_speed', name: 'DOWN Speed'},
      { value : 'upload_speed', name: 'Up Speed'},
      { value : 'average_latency', name: 'AVGLAT'},
      { value : 'packet_loss', name: 'APLOSS'},
      { value : 'jitter', name: 'Jitter'},
      { value : 'mos', name: 'MOS'},
      { value : 'updated_at', name : 'Last Login'},
      { value : 'media_device', name: 'Media Device'},
      { value : 'Throughput_percentage', name : 'Throughput' },
      { value : 'lob', name : 'LOB' },
      { value : 'programme_msa', name : 'Programme MSA' },
      { value : 'job_profile', name : 'Position' },
      { value : 'supervisor_email_id', name : 'Supervisor Email' },
      { value : 'supervisor_full_name', name : 'Supervisor'}
    ];

    public tableHeaders : Array<IHeader>  = [
      { field : 'host_name', name : 'Username' , class : 'text-left' },
      { field : 'connection_type', name : 'Connection' , class : 'text-left' },
      { field : 'net_type', name : 'AGENT' , class : 'text-left' },
      { field : 'location', name : 'Location', class : 'text-left' },
      { field : 'account', name : 'Account', class : 'text-left' },
      { field : 'mtr_highest_avg', name: 'Highest AVG', class : ''},
      { field : 'mtr_highest_loss', name: 'Highest Loss', class : ''},
      { field : 'VLAN', name: 'VLAN', class: 'text-left'},
      { field : 'DNS_1', name: 'DNS 1', class : 'text-left'},
      { field : 'DNS_2', name: 'DNS 2', class : 'text-left'},
      { field : 'subnet', name: 'Subnet', class : 'text-left'},
      { field : 'ISP', name: 'ISP', class : 'text-left'},
      { field : 'download_speed', name : 'DOWN Speed', class : '' },
      { field : 'upload_speed', name : 'UP Speed', class : '' },
      { field : 'average_latency', name : 'AVGLAT', class : '' },
      { field : 'packet_loss', name : 'APLOSS', class : '' },
      { field : 'jitter', name : 'Jitter', class : '' },
      { field : 'mos', name : 'MOS', class : '' },
      { field : 'updated_at', name : 'Last Login', class : 'text-left' },
      { field : 'Throughput_percentage', name : 'Throughput', class : 'text-left' },
      { field : 'lob', name : 'LOB', class : 'text-left' },
      { field : 'programme_msa', name : 'Programme MSA', class : 'text-left' },
      { field : 'job_profile', name : 'Position', class : 'text-left' },
      { field : 'supervisor_email_id', name : 'Supervisor Email', class : 'text-left' },
      { field : 'supervisor_full_name', name : 'Supervisor', class : 'text-left' }
    ];
    

    public filterOptions : any = {
        fields  : [
          { key : 'agent_name', name : 'Agent Name', type : 'input' },
          { key : 'account', name : 'Account' , type : 'select'},
          { key : 'VLAN',   name : 'VLAN' , type : 'select'}, 
          { key : 'DNS_1',  name : 'DNS 1' , type : 'select'}, 
          { key : 'DNS_2',  name : 'DNS 2' , type : 'select'},
          { key : 'subnet', name : 'Subnet' , type : 'select'},
          { key : 'ISP', name :'ISP', type : 'select'},
          { key : 'location', name: 'Location' , type : 'select'}
        ],
        filters : {}
      }

    constructor(
        private service : WorkstationService,
        private notification : NotificationService,
        private userConfig : UserConfigService,
        private cache : CacheService
    ) { }

    ngOnInit(): void { 
        this.paginate = this.service.getPaginate();
        this.loadData();
        this._loadColumnsConfig();

        this.service.getConnectionFilters().subscribe((filterOptions) => {
          this.filterOptions.filters = filterOptions;
        });

    }

    /*
    * Reloads the page and prompt a message
    *
    */
    private _loadColumnsConfig(){
      const selectedColumns = this.userConfig.get('USER_AGENT_COLUMNS_INACTIVE_PAGE');

      if(!selectedColumns) return;

      this.selectedColumns = selectedColumns;
    }

    public invalidateRefresh(){
      this.paginate.currentPage = 1;
      this.clearCache = true;
      this.skipCache = false;
      this.loadData();
    }

    /*
   * Handles the search function
   */
  public search( data  ){
    const self = this;
    const params = data.params;
    this.clearCache = true;

    Object.keys( params ).forEach( (val, index ) => {
      if( !params[val] ) return;

      const filterKeyValue = [];

      filterKeyValue[`${val}[]`] = params[val];

      self.filters = Object.assign( self.filters, filterKeyValue );
    });

    this.loadData();
    self.notification.success('Idle has device has been filtered.');
    self.hasFiltered = true;
  }

    /*
    * Handles resetting the search
    */
    public reset(){
        // this.router.navigate(['/connected-agents'] )
        this.hasFiltered = false;
        this.clearCache = true;
        this.filters = {};
        this.loadData();
    }

    public loadData(){
        const self = this;
        if( this._loadCache() )
          return;

        self.isLoading = false;
        this.service.getInactiveAgents( this.paginate.currentPage, this.paginate.perPage, this.filters ).subscribe( (data) => {
            self.updateCounter = 0;
            self.connectedAgents = data;
            self.paginate  = self.service.getPaginate();

            self.notification.success('Idle devices has been reloaded');

            if( self.skipCache ) return;
            self.cache.save('INACTIVE_AGENTS', {
                data : self.connectedAgents,
                paginate : self.paginate
            });
            // self.cache.store('CACHE_INACTIVE_AGENTS', {
            //   data : self.connectedAgents,
            //   paginate : self.paginate
            // });
      });
    }

    private _loadCache(){
      const self = this;
      const cacheData = this.cache.get('CACHE_INACTIVE_AGENTS');

      if( !cacheData || this.clearCache || self.skipCache ) return false;
      self.isLoading = false;
      this.cache.countUpdatedData('INACTIVE_AGENTS').subscribe( response => {
        self.connectedAgents = cacheData['args']['data'];
        self.paginate = cacheData['args']['paginate'];
        
        self.updateCounter = Math.abs( self.paginate['total'] - response['total'] )
      });

      return true;
    }
    // const self = this;
    // const cacheData = this.cache.get('CACHE_CONNECTED_AGENTS');

    // if( !cacheData || this.clearCache ) return false;

    // this.cache.countUpdatedData('CONNECTED_AGENTS').subscribe( response => {
    //   self.connectedAgents = cacheData['args']['data'];
    //   self.paginate = cacheData['args']['paginate'];
    //   self.isLoading = false;
    //   self.updateCounter = Math.abs( self.paginate['total'] - response['total'] );
    // });

    // return true;


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

    public speedtestOptions = [
      { value: 'all', name: 'All results' },
      { value: 'threshold', name: 'Low Speedtest/MOS' },
    ];

    public onSpeedtestFilter(value) {
      const selected = _.find(this.speedtestOptions, { value: value })
  
      if (value == this.speedtestFilter) return;
      
      this.skipCache = true;
  
      this.speedtestFilter = value;
  
      this.filters = Object.assign(this.filters, { speedtest: value });
      
      this.loadData();
  
      this.notification.success(`Displaying ${selected.name}...`);
    }

    /*
    * Dynamic column handler
    */
    public onDynamicColumnChange( selectedColumns ){
      this.selectedColumns = selectedColumns;
      this.userConfig.set('USER_AGENT_COLUMNS_INACTIVE_PAGE', selectedColumns );
    }
}
