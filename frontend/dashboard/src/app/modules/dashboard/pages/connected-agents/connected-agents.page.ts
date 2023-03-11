import { Component, OnInit } from '@angular/core';
import * as _ from 'lodash';
import {
	WorkstationService,
	SocketService,
	EventLogsService,
	NotificationService,
	UserConfigService,
	CacheService,
	AuthService
} from '@app/services';
import { IPaginate, IAgent } from '@app/interfaces';
import { ActivatedRoute, Router, Params } from '@angular/router';
import { DomSanitizer } from '@angular/platform-browser';
import { WorkstationDetailsComponent, ApplicationMonitoringComponent, AgentProfileComponent, WebMTRComponent, NewWorkstationProfileComponent } from '@modules/dashboard/components';
import { MatDialog } from '@angular/material/dialog';
import { PopupIntervalComponent } from '../../components';
import { interval } from 'rxjs';
import { DatePipe } from '@angular/common';
import {FormControl} from '@angular/forms';
import swal from 'sweetalert2';

interface IHeader {
	field: string,
	name: string,
	class: string
}

@Component({
	selector: 'connected-agents-page',
	templateUrl: './connected-agents.page.html',
	styleUrls: ['./connected-agents.page.css']
})
export class ConnectedAgentsPage implements OnInit {

	opened = false;
	searchBar = new FormControl('');
	search_value = '';
	public paginate: IPaginate;
	public userToken : any;
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

	public hasFiltered: Boolean = false;

    public readOnly : Boolean = this.auth.isAllowed('users:read-only');

	public filters: any = {};
	public filterOptions: any = {
		fields: [
			{ key: 'agent_name', name: 'Agent Name', type: 'input' },
			{ key: 'account', name: 'Account', type: 'select' },
			{ key: 'VLAN', name: 'VLAN', type: 'select' },
			{ key: 'DNS_1', name: 'DNS 1', type: 'select' },
			{ key: 'DNS_2', name: 'DNS 2', type: 'select' },
			{ key: 'subnet', name: 'Subnet', type: 'select' },
			{ key: 'ISP', name: 'ISP', type: 'select' },
			{ key: 'location', name: 'Location', type: 'select' }
		],
		filters: {}
	}

	public flagHeaders: Array<IHeader> = [
		{ field: 'host_name', name: 'Username', class: 'text-left' },
		{ field: 'connection_type', name: 'Connection', class: 'text-left' },
		{ field: 'net_type', name: 'AGENT', class: 'text-left' },
		{ field: 'station_number', name: 'Workstation', class: 'text-left' },
		{ field: 'position', name: 'Position', class: '' },
		{ field: 'location', name: 'Location', class: 'text-left' },
		{ field: 'account', name: 'Account', class: 'text-left' },
		{ field: 'manager', name: 'Manager', class: '' },
		{ field: 'mtr_highest_avg', name: 'Highest AVG', class: '' },
		{ field: 'mtr_highest_loss', name: 'Highest Loss', class: '' },
		{ field: 'host_ip_address', name: 'IP', class: '' },
		{ field: 'vpn', name: 'VPN', class: '' },
		{ field: 'VLAN', name: 'VLAN', class: 'text-left' },
		{ field: 'DNS_1', name: 'DNS 1', class: 'text-left' },
		{ field: 'DNS_2', name: 'DNS 2', class: 'text-left' },
		{ field: 'subnet', name: 'Subnet', class: 'text-left' },
		{ field: 'ISP', name: 'ISP', class: 'text-left' },
		{ field: 'download_speed', name: 'DOWN Speed', class: '' },
		{ field: 'upload_speed', name: 'UP Speed', class: '' },
		{ field: 'average_latency', name: 'AVGLAT', class: '' },
		{ field: 'packet_loss', name: 'APLOSS', class: '' },
		{ field: 'jitter', name: 'Jitter', class: '' },
		{ field: 'mos', name: 'MOS', class: '' },
		{ field: 'updated_at_aging', name: 'Last Login', class: '' },
		{ field: 'updated_at', name: 'Timestamp', class: '' },
		{ field: 'Throughput_percentage', name: 'Throughput', class: 'text-left' },
		{ field: 'lob', name: 'LOB', class: '' },
		{ field: 'programme_msa', name: 'Programme MSA', class: '' },
		// { field: 'job_profile', name: 'Job Profile', class: '' },
		{ field: 'supervisor_email_id', name: 'Manager Email', class: '' },
		// { field: 'supervisor_full_name', name: 'Supervisor', class: '' }
	];

	public sortedField = null;

	/*
	 * Handles the sorting of reported issues
	 */
	public sortFlag(event) {
		this.sortedField = event['field'];
		this.service.setSort(event['field'], event['direction']);
		this.clearCache = true;
		this.skipCache = false;

		this.loadData();
	}

	public selectedColumns: Array<string> = [
		'connection_type', 'net_type', 'location',
		'account', 'status_info', 'download_speed',
		'upload_speed', 'average_latency',
		'packet_loss', 'jitter', 'mos'
	];

	public tableColumns = [
		{ value: 'host_name', name: 'Username' },
		{ value: 'connection_type', name: 'Connection' },
		{ value: 'net_type', name: 'Agent' },
		{ value: 'station_number', name: 'Workstation' },
		{ value: 'position', name: 'Position' },
		{ value: 'location', name: 'Location' },
		{ value: 'account', name: 'Account' },
		{ value: 'manager', name: 'Manager' },
		{ value: 'mtr_highest_avg', name: 'Highest AVG' },
		{ value: 'mtr_highest_loss', name: 'Highest Loss' },
		{ value: 'host_ip_address', name: 'IP' },
		{ value: 'vpn', name: 'VPN' },
		{ value: 'VLAN', name: 'VLAN' },
		{ value: 'DNS_1', name: 'DNS 1' },
		{ value: 'DNS_2', name: 'DNS 2' },
		{ value: 'subnet', name: 'Subnet' },
		{ value: 'ISP', name: 'ISP' },
		{ value: 'download_speed', name: 'DOWN Speed' },
		{ value: 'upload_speed', name: 'Up Speed' },
		{ value: 'average_latency', name: 'AVGLAT' },
		{ value: 'packet_loss', name: 'APLOSS' },
		{ value: 'jitter', name: 'Jitter' },
		{ value: 'mos', name: 'MOS' },
		{ value: 'updated_at_aging', name: 'Last Login' },
		{ value: 'updated_at', name: 'Timestamp' },
		{ value: 'Throughput_percentage', name: 'Throughput' },
		{ value: 'media_device', name: 'Media Device' },
		{ value: 'lob', name: 'LOB' },
		{ value: 'programme_msa', name: 'Programme MSA' },
		// { value: 'job_profile', name: 'Job Profile' },
		{ value: 'supervisor_email_id', name: 'Manager Email' },
		// { value: 'supervisor_full_name', name: 'Supervisor' },
		{ value: 'securecx_gate_1', name: 'SecureCX Gate1'},
		{ value: 'securecx_gate_2', name: 'SecureCX Gate2'},
		{ value: 'securecx_gate_3', name: 'SecureCX Gate3'}

	];

	public filterData = [
		{ value: 'clear', name: 'Clear All' },
		{ value: 'connection_type', name: 'Connection' },
		{ value: 'net_type', name: 'Agent' },
		{ value: 'account', name: 'Account' },
		{ value: 'vlan', name: 'VLAN' },
		{ value: 'dns_1', name: 'DNS_1' },
		{ value: 'dns_2', name: 'DNS_2' },
		{ value: 'subnet', name: 'Subnet' },
		{ value: 'ISP', name: 'ISP' },
		{ value: 'mos', name: 'MOS' },
		{ value: 'lob', name: 'LOB' },
		{ value: 'programme_msa', name: 'Programme MSA' },
		{ value: 'position', name: 'Position' }
	];

	public reportType = [
		{ value: 'excel', name: 'Export to Excel' },
		{ value: 'pdf', name: 'Export to PDF' },
		{ value: 'interval', name: '30 Mins Interval' },
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
		private route: ActivatedRoute,
		private router: Router,
		private sanitizer: DomSanitizer,
		private socket: SocketService,
		private eventLog: EventLogsService,
		private dialog: MatDialog,
		private notification: NotificationService,
		private userConfig: UserConfigService,
		private cache: CacheService,
		private auth: AuthService
	) { 
	
	}


	/*
	 * Executes on loading of page
	 */
	ngOnInit() {
		const self = this;

		this._loadColumnsConfig();

		this.paginate = this.service.getPaginate();

		self.loadData();

		this.service.getConnectionFilters().subscribe((filterOptions) => {
			self.filterOptions.filters = filterOptions;
		});

		this._listenForNewConnections();
		this._setInitialUserTimezone();
	}

	public searchValue()
	{
  
		this.isLoading = true;
		this.service.getConnectedAgents(this.paginate.currentPage, this.paginate.perPage, this.filters, this.searchBar.value).subscribe((data) => {
			this.connectedAgents = data;
			this.paginate = this.service.getPaginate();
			this.isLoading = false;	
		});
	}

	/*
	 * Reloads the page and prompt a message
	 *
	 */
	private _loadColumnsConfig() {
		const selectedColumns = this.userConfig.get('USER_AGENT_COLUMNS_CONNECTED_PAGE');

		if (!selectedColumns) return;

		this.selectedColumns = selectedColumns;
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
				self.invalidateRefresh();
			}
		}, 1000);
	}


	/*
	 * Connect with socket to check if
	 * there are new agent connections
	 */
	private _listenForNewConnections() {
		const self = this;

		this.socket.connect();
		this.socket.bind('dashboard:agent-list', (data) => {
			self.updateCounter++;
		});
	}

	/*
	 * Display the MTR Tooltip
	 */
	public updateTooltipContent(agent: IAgent) {
		this.mtrTooltipTitle = `MTR Result : ${agent.agent_name} (${agent.mtr_host})`;
		this.mtrTooltipContent = agent.mtr_result;
	}

	/*
	 * Loads the conencted agent by page number
	 */
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


		if (this._loadCache()){
			return;
		}
		
			
		this.service.getConnectedAgents(this.paginate.currentPage, this.paginate.perPage, this.filters, this.searchBar.value).subscribe((data) => {
			self.updateCounter = 0;
			self.connectedAgents = data;
			self.userToken = self.service.getUserToken();
			self.paginate = self.service.getPaginate();
			self.isLoading = false;
			if (self.currentFilterSelected !== 'clear') {
				self.onFilterChange(self.currentFilterSelected);
			}
			self.notification.success('Connected devices has been reloaded');

			if (self.skipCache) return;

			self._saveCache();
		});
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
			self.userToken = self.service.getUserToken();
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

	// select type of report
	public setReport(type) {

		let userReportTimezone = localStorage.getItem('USER_TIMEZONE_NAME');

		if (type == 'interval') {
			this.showIntervalReportsForm();
			return;
		}

		this.isLoading = true;
		this.service.downloadReport(type, this.filters, this.selectedColumns, userReportTimezone).subscribe((data) => {
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
				let fullDateFormat = currentDate.getFullYear() + month + date;

				if (type === 'excel') {
					filename = `sentrycx_connected_agents_${fullDateFormat}.xlsx`;
				} else {
					filename = `sentrycx_connected_agents_${fullDateFormat}.pdf`;
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

	public showIntervalReportsForm() {
		const modal = this.dialog.open(PopupIntervalComponent);
		const self = this;
		const datepipe: DatePipe = new DatePipe('en-US')

		modal.componentInstance['onDownload'].subscribe(intervalForm => {
			const params = {
				date_start: datepipe.transform(intervalForm['date_start'], 'yyyy-MM-dd 00:00:00'),
				date_end: datepipe.transform(intervalForm['date_end'], 'yyyy-MM-dd 23:59:59'),
			};


			if (intervalForm['type'] == 'SUMMARY') {
				self.service.downloadIntervalSummaryReport(params).subscribe(data => {
					self._generateFile('sentrycx_interval_summary', data)
				});
				return;
			}



			self.service.downloadIntervalDetailReport(params).subscribe(data => {
				self._generateFile('sentrycx_interval_detail', data);
			});

		});
	}

	private _generateFile(type, data) {
		let filename = null;
		let currentDate = new Date();
		let month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
		let date = ("0" + currentDate.getDate()).slice(-2);
		let fullDateFormat = currentDate.getFullYear() + month + date;
		filename = `${type}_${fullDateFormat}.xlsx`;

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

	private _loadPacDetails(componentInstance, url) {
		const self = this;
		self.service.getPACDetails(url).subscribe(response => {
			componentInstance['pacDetails'] = response['data']
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
		this.searchValue();
	}
}
