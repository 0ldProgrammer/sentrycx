import { Component, OnInit } from '@angular/core';
import { WorkstationService, SocketService ,NotificationService,
    UserConfigService } from '@app/services';
import { IPaginate } from '@app/interfaces';
import * as _ from 'lodash';

interface IField {
    id : string;
    name : string;
}

interface IHeader {
    field : string,
    name : string,
    class : string 
  }

@Component({
    selector: 'connected-toc-page',
    templateUrl: 'connected-toc.page.html',
    styleUrls: ['./connected-toc.page.css']
})

export class ConnectedTOCPage implements OnInit {
    public paginate : IPaginate;
    public filters : any = { aux_status : 'ACTIVE'};
	public isLoading: Boolean = false;
	public userTimezone: string = '';

    public connectedEngineers : Array<any> = [];

    public STATUS_MAPPING = {
        ACTIVE : 'ON DUTY',
        INACTIVE : 'OFF DUTY'
    }

    public statusTitle = 'ONLINE ITSS';

    public statusOptions = [
        { value : 'ACTIVE', name : 'ONLINE ITSS' },
        { value : 'INACTIVE', name : 'OFFLINE ITSS' },
        { value : 'ALL', name : 'ALL ITSS'}
      ];

    public flagHeaders : Array<IHeader>  = [
        { field : 'agent_name', name : 'Name' , class : 'text-left' },
        { field : 'country', name : 'Country' , class : 'text-left' },
        { field : 'location', name : 'Location' , class : 'text-left' },
        { field : 'account', name : 'Account' , class : 'text-left' },
        { field : 'agent_email', name : 'Agent Email' , class : 'text-left' },
        { field : 'worker_id', name : 'Worker ID' , class : 'text-left' },
        { field : 'host_ip_address', name : 'IP Address' , class : 'text-left' },
        { field : 'station_number', name : 'Station No.' , class : 'text-left' },
        { field : 'aging', name : 'Aging' , class : 'text-left' },
        { field : 'timestamp', name : 'Timestamp' , class : 'text-left' },
        { field : 'status', name : 'Status' , class : 'text-left' },
    ]

    public tableColumns = [
        { value : 'agent_name', name : 'Name' },
        { value : 'country', name : 'Country' },
        { value : 'location', name : 'Location' },
        { value : 'account', name : 'Account' },
        { value : 'agent_email', name : 'Agent Email' },
        { value : 'worker_id', name : 'Worker ID' },
        { value : 'host_ip_address', name : 'IP Address'},
        { value : 'station_number', name : 'Station No.' },
        { value : 'aging', name : 'Aging' },
        { value : 'timestamp', name : 'Timestamp' },
        { value : 'status', name : 'Status' },

    ]

    public selectedColumns : Array<string> = [
        'agent_name', 'country','location',
        'account', 'agent_email','worker_id'
    ]

    public DATA_FIELDS : Array<IField> = [
        { id : 'country', name:'Country' },
        { id : 'location', name : 'Location' },
        { id : 'account', name : 'Account' },
        { id : 'agent_email', name : 'Email' },
        { id : 'worker_id', name :'Employee ID' },
        { id : 'host_ip_address', name :'IP Address' },
        { id : 'station_number', name : 'Station No.'}
    ];

    public reportType = [
        { value : 'excel', name: 'Export to Excel'},
        { value : 'pdf', name: 'Export to PDF'}
      ];


    constructor( 
        private service : WorkstationService,
        private userConfig : UserConfigService,
        private socket  : SocketService,
        private notification : NotificationService
    ) { }

    ngOnInit() { 
        const self = this;
    
        this._loadColumnsConfig();

        this.paginate = this.service.getPaginate();

        this.loadData();

		this._listenForNewConnections();
		
		this._setInitialUserTimezone();
    }


  /*
   * Dynamic column handler
   */
  public onDynamicColumnChange( selectedColumns ){
    this.selectedColumns = selectedColumns;
    this.userConfig.set('USER_CONNECTED_ITSS_PAGE', selectedColumns );
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
  private _loadColumnsConfig(){
    const selectedColumns = this.userConfig.get('USER_CONNECTED_ITSS_PAGE');

    if(!selectedColumns) return;

    this.selectedColumns = selectedColumns;
  }

    /*
    * Filters the TOC based on AUX status
    */
    public onStatusFilter( value ) {
        const selected = _.find(this.statusOptions, { value : value })
        this.statusTitle = selected.name;

        this.filters = Object.assign( this.filters, { aux_status: value } );
        this.loadData();
        this.notification.success('Connections has been filtered.');
      }

    /*
    * Loads the list of connected agents
    */
    public loadData(){
        const self = this;

        this.isLoading = true;

        this.service.getConnectedEngineers(this.paginate.currentPage, this.paginate.perPage, this.filters ).subscribe( (data) => {
            self.connectedEngineers = data;
            self.paginate = self.service.getPaginate();
            self.isLoading = false;
        });
    }

     // select type of report
  public setReport( type ){

    let userReportTimezone = localStorage.getItem('USER_TIMEZONE_NAME');

    this.service.downloadReportFromConnectedTOC( type, this.filters, this.selectedColumns, userReportTimezone ).subscribe( (data) => {
      let filename = null;
      let currentDate = new Date();
      let month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
      let date = ("0" + currentDate.getDate()).slice(-2);
      let fullDateFormat =  currentDate.getFullYear() + month + date;
      
      if (type === 'excel') {
        filename = `sentrycx_connected_toc_${fullDateFormat}.xlsx`;
      } else {
        filename = `sentrycx_connected_toc_${fullDateFormat}.pdf`;
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
    * Loads the conencted agent by page number
    */
    public updateData( pageDetails ){
        this.paginate.currentPage = pageDetails.pageIndex + 1;
        this.paginate.perPage = pageDetails.pageSize;

        this.loadData();
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
}