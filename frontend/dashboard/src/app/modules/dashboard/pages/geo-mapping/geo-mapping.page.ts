import { Component, OnInit } from '@angular/core';
import * as _ from 'lodash';
import {
	WorkstationService,
	SocketService,
	EventLogsService,
	NotificationService,
	UserConfigService,
	CacheService
} from '@app/services';
import { IPaginate, IAgent } from '@app/interfaces';
import { DomSanitizer } from '@angular/platform-browser';
import { WorkstationDetailsComponent, ApplicationMonitoringComponent, AgentProfileComponent, WebMTRComponent, NewWorkstationProfileComponent } from '@modules/dashboard/components';
import { MatDialog } from '@angular/material/dialog';
import { PopupIntervalComponent } from '../../components';
import { DatePipe } from '@angular/common';

interface IHeader {
	field: string,
	name: string,
	class: string
}

@Component({
    selector: 'geo-mapping',
    templateUrl: './geo-mapping.page.html',
	styleUrls: ['./geo-mapping.page.css']
})

export class GeoMappingPage implements OnInit {

	opened = false;

	public paginate: IPaginate;
	public connectedAgents: Array<IAgent> = [];
	public filteredData = [];
	public mtrTooltipTitle: String = 'MTR Result : ';
	public mtrTooltipContent: String = '<br />';
	public clearCache: Boolean = false;
	public skipCache: Boolean = false;
	public isLoading: Boolean = true;
	public updateCounter: number = 0;
	public currentFilterSelected: string = '';
	public userTimezone: string = '';
    public active : number = 0;
    public complete_address = "Preparing to extract Address";
	public last_week_address = "Preparing to extract Address"

	public hasFiltered: Boolean = false;
	public filters: any = {};
	public filterOptions: any = {
		fields: [
			{ key: 'account', name: 'Account', type: 'select' },
			{ key: 'city', name: 'City', type: 'select' },
			{ key: 'country', name: 'Country', type: 'select' },
			{ key: 'location', name: 'Location', type: 'select' }
		],
		filters: {}
	}

	public flagHeaders: Array<IHeader> = [
		{ field: 'host_name', name: 'Username', class: 'text-left' },
		{ field: 'station_name', name: 'Workstation Name', class: 'text-left' },
		{ field: 'connection_type', name: 'Connection', class: 'text-left' },
		{ field: 'workstation_type', name: 'Workstation Type', class: 'text-left'},
		{ field: 'net_type', name: 'AGENT', class: 'text-left' },
		{ field: 'position', name: 'Position', class: '' },
		{ field: 'location', name: 'Location', class: 'text-left' },
		{ field: 'account', name: 'Account', class: 'text-left' },
		{ field: 'ISP', name: 'ISP', class: 'text-left' },
		{ field: 'download_speed', name: 'DOWN Speed', class: '' },
		{ field: 'upload_speed', name: 'UP Speed', class: '' }
	];

	public sortedField = null;

	/*
	 * Handles the sorting of reported issues
	 */
	public sortFlag(event) {
		this.sortedField = event['field'];

		this.service.setSort(event['field'], event['direction']);

		this.loadData();
	}

	public selectedColumns: Array<string> = [
		'station_name','workstation_type','account', 'location', 'is_active'
	];

	public tableColumns = [
		{ value: 'host_name', name: 'Username' },
		{ value: 'connection_type', name: 'Connection' },
		{ value: 'station_name', name: 'Workstation Name' },
		{ value: 'workstation_type', name: 'Workstation Type'},
		{ value: 'position', name: 'Position' },
		{ value: 'location', name: 'Location' },
		{ value: 'account', name: 'Account' },
		{ value: 'is_active', name: 'Status' },
		{ value: 'ISP', name: 'ISP' }
	];

	public filterData = [
		{ value: 'clear', name: 'Clear All' },
		{ value: 'connection_type', name: 'Connection' },
		{ value: 'net_type', name: 'Agent' },
		{ value: 'account', name: 'Account' },
	];

	/*
	 * Dynamic column handler
	 */
	public onDynamicColumnChange(selectedColumns) {
		this.selectedColumns = selectedColumns;
		this.userConfig.set('USER_AGENT_COLUMNS_CONNECTED_PAGE', selectedColumns);
	}


	private popupConfig = {
		minWidth: '720px',
		minHeight: '600px'
	}

	public agentStatusTitle = 'Online Agents';

	public agentStatusOptions = [
		{ value: 'online', name: 'Online Agents' },
		{ value: 'offline', name: 'Offline Agents' },
		{ value: 'all', name: 'All Agents' }
	];

	public onAgentStatusFilter(value) {
		const selected = _.find(this.agentStatusOptions, { value: value })
		this.agentStatusTitle = selected.name;
		this.filters = Object.assign(this.filters, { connection: value });
		this.loadData();
		this.notification.success('Connections has been filtered.');
	}


	public speedtestTitle = "<i class='material-icons'>more_horiz</i>";

	public speedtestOptions = [
		{ value: 'all', name: 'All results' },
		{ value: 'threshold', name: 'Low Speedtest/MOS' },
	];

	public speedtestFilter = 'all';

	public onSpeedtestFilter(value) {
		const selected = _.find(this.speedtestOptions, { value: value })

		if (value == this.speedtestFilter) return;

		this.skipCache = true;

		this.speedtestFilter = value;

		this.filters = Object.assign(this.filters, { speedtest: value });
		
		this.loadData();

		this.notification.success(`Displaying ${selected.name}...`);
	}

	public invalidateRefresh() {
		this.paginate.currentPage = 1;
		this.clearCache = true;
		this.skipCache = false;
		this.paginate.perPage = 20;
		this.loadData();
	}

	/*
	 * Constrcutor dependencies
	 */
	constructor(
		private service: WorkstationService,
		private sanitizer: DomSanitizer,
		private eventLog: EventLogsService,
		private dialog: MatDialog,
		private notification: NotificationService,
		private userConfig: UserConfigService,
		private cache: CacheService
	) { }


	/*
	 * Executes on loading of page
	 */
	ngOnInit() {
		const self = this;

		// this._loadColumnsConfig();

		this.paginate = this.service.getPaginate();

		self.loadData();

		this.service.getGeoMappingFilters().subscribe((filterOptions) => {
			self.filterOptions.filters = filterOptions;
		});
	}

	public updateData(pageDetails) {
		this.paginate.currentPage = pageDetails.pageIndex + 1;
		this.paginate.perPage = pageDetails.pageSize;
		this.skipCache = true;
		this.isLoading = true;
		this.loadData();
	}

	/*
	 * Loads the list of connected agents
	 */
	public loadData() {
		
		const self = this;


		// if (this._loadCache())
		// 	return;

			
		this.service.getGeoMappingList(this.paginate.currentPage, this.paginate.perPage, this.filters).subscribe((data) => {
			self.updateCounter = 0;
			self.connectedAgents = data['details'].data as Array<IAgent>;
            self.active = data['active'];
			self.paginate = self.service.getPaginate();
			self.isLoading = false;
			// if (self.currentFilterSelected !== 'clear') {
			// 	self.onFilterChange(self.currentFilterSelected);
			// }
			// self.notification.success('Connected devices has been reloaded');
			
			// if (self.skipCache) return;

			// self._saveCache();
		});
	}

    public getLocation(long, lat, week_long, week_lat)
    {
        this.service.getLocation(long,lat).subscribe((data) => {

            this.complete_address = data['display_name'];
        })
        this.service.getLocation(week_long,week_lat).subscribe((details) => {

            this.last_week_address = details['display_name'];
        })
    }

	/*
	 * TODO : This function is redundant
	 * Clears the existing cache
	 *  and store the udpated one
	 */
	private _saveCache() {
		this.cache.invalidateUpdatedData('CONNECTED_AGENTS');
		this.cache.store('CACHE_CONNECTED_AGENTS', {
			data: this.connectedAgents,
			paginate: this.paginate
		});
	}

	/*
	 * Checks if there are data from cache
	 * Returns false if no cache
	 */
	private _loadCache() {
		const self = this;
		const cacheData = this.cache.get('CACHE_CONNECTED_AGENTS');

		if (!cacheData || this.clearCache || self.skipCache) return false;
		self.isLoading = false;
		this.cache.countUpdatedData('CONNECTED_AGENTS').subscribe(response => {
			self.connectedAgents = cacheData['args']['data'];
			self.paginate = cacheData['args']['paginate'];
			self.updateCounter = Math.abs(self.paginate['total'] - response['total']);
		});

		return true;
	}

	public tooltipMarkup(content) {
		return this.sanitizer.bypassSecurityTrustHtml(content);
	}

	/*
	 * Handles the search function
	 */
	public search(data) {
		const self = this;
		const params = data.params;
		this.clearCache = true;
		
		Object.keys(params).forEach((val, index) => {
			if (!params[val]) return;

			const filterKeyValue = [];

			filterKeyValue[`${val}[]`] = params[val];

			self.filters = Object.assign(self.filters, filterKeyValue);
		});

		this.loadData();
		self.notification.success('Connections has been filtered.');
		self.hasFiltered = true;
	}

	/*
	 * Handles resetting the search
	 */
	public reset() {
		// this.router.navigate(['/connected-agents'] )
		this.hasFiltered = false;
		this.clearCache = true;
		this.filters = {};
		this.loadData();
	}

	
	/* 
	 * Load the workstation application monitoring popup
	 * of the selected workerID
	 *
	 */
	public loadAplicationMonitoring(agent) {
		const self = this;
		const modal = self.dialog.open(ApplicationMonitoringComponent, self.popupConfig);

		modal.componentInstance['agentName'] = agent.agent_name;
		// this.service.getApplicationStatus( agent.worker_id , agent.account ).subscribe( (data) => {
		//   modal.componentInstance['agentApplications'] = data['applications'];
		//   modal.componentInstance['automaticApplicationStatus'] = _.filter( data['data'], { type : 'AUTO' } )//data['data'];
		//   modal.componentInstance['manualApplicationStatus']    = _.filter( data['data'], { type : 'MANUAL' } )//data['data'];
		// });


		modal.componentInstance['onExtract'].subscribe((application) => {
			self.service.extractApplicationStatistics(agent.worker_id, application);
		});
	}



	/* 
	 * Load the agent profile of the selected workerID
	 *
	 */
	public loadAgentProfile(workerID) {
		const self = this;

		const modal = self.dialog.open(AgentProfileComponent, self.popupConfig);

		modal.componentInstance['workerID'] = workerID;
	}
	/* 
	 * Load the workstation profile of the selected workerID
	 *
	 */
	public loadWorkstation(args) {
		const agentName = args['agent_name'];
		const workerID = args['worker_id'];

		const self = this;
		const modal = self.dialog.open(WorkstationDetailsComponent, self.popupConfig);

		let params = {
			componentInstance: modal.componentInstance, 
			flagID    : 1,
			agentName : agentName,
			workerID: workerID,
			pageNo    : 1,
		}


		this._bindWorkstationPopupEvents(params);
    
        modal.componentInstance['isLoading']    = true;
	}
	/*
	 * Bind events to workstation popup
	 */
	private _bindWorkstationPopupEvents(params) {
		const self = this;
		const workerID = params['workerID'];
		const agentName = params['agentName'];
		const redFlagID = params['flagID'];
		const componentInstance = params['componentInstance'];

		// TODO :  Move this into service call to workstation-detilas
		this.service.getAgentWorkstation(workerID).subscribe((data) => {
			let hardwareInfo = false;
			if (data['data'].length > 0)
				hardwareInfo = data['data'][0];

			componentInstance['hardwareInfo'] = hardwareInfo;
			componentInstance['agentName'] = agentName;
			componentInstance['lezap'] = data['lezap'];
			componentInstance['isLoading'] = false;
			componentInstance['eventLogs'] = data['event-logs'];
			componentInstance['singleInfo'] = false;
			componentInstance['remoteSessionLogs'] = data['zoho-logs'];
			componentInstance['mediaDevices'] = data['media-devices'];
			componentInstance['speedtest'] = data['speedtest'];
			componentInstance['paginate']     = self.service.getPaginate();

			//self._loadPacDetails( componentInstance, hardwareInfo['pac_address']);
		});

		componentInstance['onRequest'].subscribe( (requestParams) => {
			componentInstance['progress'] = 1;
			self.service.requestWorkstationProfile( redFlagID, requestParams );

		});

		componentInstance['onEventExtract'].subscribe((logParams) => {
			self.eventLog.init(params['workerID'], logParams).then((response) => {
				componentInstance['eventLogs'].push(response['filename']);
			});
		});

		componentInstance['onAgentLock'].subscribe((sessionID) => {
			self.service.lockAgentWorkstation(workerID, sessionID);
		});

		componentInstance['onAgentWipeout'].subscribe((workerID) => {
			self.service.wipeoutAgentWorkstation(workerID);
		});

		componentInstance['onMediaStatsRequest'].subscribe((workerID) => {
			self.service.requestForMediaDeviceStats(workerID);
			// self.notification.success("Device Check Notification has been sent to the agent.");
		});

	}

	public onFilterChange(selectedFilter) {
		const self = this;
		self.currentFilterSelected = selectedFilter;
		if (selectedFilter === 'clear') {
			self.filteredData = [];
		} else {
			self.service.getBreakdownData(selectedFilter, this.filters).subscribe((data) => {
				self.filteredData = data;
				self.isLoading = false;
			});
		}
	}

	public loadWebMTR(agentId) {
		const self = this;

		const modal = self.dialog.open(WebMTRComponent, { panelClass: 'custom-full-dialog' });

		modal.componentInstance['agentId'] = agentId;
	}

	public loadNewWorkstationProfile(agent) {
		const agentName = agent['agent_name'];
		const workerID = agent['worker_id'];
		
		const self = this;
		const modal = self.dialog.open(NewWorkstationProfileComponent, {
			panelClass: 'custom-new-workstation'
		});

		let params = {
			componentInstance: modal.componentInstance, 
			flagID    : 1,
			agentName : agentName,
			workerID: workerID,
			pageNo    : 1,
		}


		this._bindWorkstationPopupEvents(params);
    
        modal.componentInstance['isLoading']    = true;
	}
}
