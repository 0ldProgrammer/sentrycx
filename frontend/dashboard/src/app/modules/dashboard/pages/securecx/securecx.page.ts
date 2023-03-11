import { Component, OnInit } from '@angular/core';
import * as _ from 'lodash';
import {
	WorkstationService,
	NotificationService,
	UserConfigService,
	HistoricalService
} from '@app/services';
import { IPaginate, IAgent } from '@app/interfaces';
import { DomSanitizer } from '@angular/platform-browser';
import {FormControl} from '@angular/forms';

interface IHeader {
	field: string,
	name: string,
	class: string
}

@Component({
	selector: 'securecx-page',
	templateUrl: './securecx.page.html',
	styleUrls: ['./securecx.page.css']
})
export class SecurecxPage implements OnInit {

	opened = false;
	searchBar = new FormControl('');
	search_value = '';
	public initialData : Array<String> = [];
	public paginate: IPaginate;
	public connectedAgents: Array<IAgent> = [];
	public filteredData = [];
	public mtrTooltipTitle: String = 'MTR Result : ';
	public mtrTooltipContent: String = '<br />';
	public isLoading: Boolean = true;
	public updateCounter: number = 0;
	public currentFilterSelected: string = '';
	public userTimezone: string = '';
	public securecxUrls;

	public hasFiltered: Boolean = false;
	public filters: any = {};
	public filterOptions: any = {
		fields: [
			{ key: 'agent_name', name: 'Agent Name', type: 'input' },
			{ key: 'account', name: 'Account', type: 'select' },
			{ key: 'location', name: 'Location', type: 'select' }
		],
		filters: {}
	}

	public flagHeaders: Array<IHeader> = [
		{ field: 'host_name', name: 'Username', class: 'text-left' },
		{ field: 'connection_type', name: 'Connection', class: 'text-left' },
		{ field: 'net_type', name: 'Net Type', class: 'text-left' },
		{ field: 'station_number', name: 'Workstation', class: 'text-left' },
		{ field: 'location', name: 'Location', class: 'text-left' },
		{ field: 'account', name: 'Account', class: 'text-left' }
	];

	public reportType = [
		{ value: 'excel', name: 'Export to Excel' }
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
		'host_name', 'connection_type', 'net_type', 'station_number', 'location', 'account'
	];

	/*
	 * Constrcutor dependencies
	 */
	constructor(
		private service: WorkstationService,
		private sanitizer: DomSanitizer,
		private notification: NotificationService,
		private userConfig: UserConfigService,
		private historical: HistoricalService
	) { 
	
	}


	/*
	 * Executes on loading of page
	 */
	ngOnInit() {
		const self = this;

		this.paginate = this.service.getPaginate();

		self.loadData();

		this.service.getSecurecxFilters().subscribe((filterOptions) => {
			self.filterOptions.filters = filterOptions;
		});
	}

	public searchValue()
	{
  
		this.isLoading = true;
		this.service.getSecurecxAgents(this.paginate.currentPage, this.paginate.perPage, this.filters, this.searchBar.value).subscribe((data) => {
			this.connectedAgents = data;
			this.paginate = this.service.getPaginate();
			this.isLoading = false;	
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
		this.isLoading = true;
		this.loadData();
	}

	/*
	 * Loads the list of connected agents
	 */
	public loadData() {
		
		const self = this;

		this.service.getSecurecxAgents(this.paginate.currentPage, this.paginate.perPage, this.filters, this.searchBar.value).subscribe((data) => {
			self.updateCounter = 0;
			self.connectedAgents = data;
			self.paginate = self.service.getPaginate();
			self.isLoading = false;
		});
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
		this.searchValue();
	}

	public legendList() {
		return "P1 - Streaming Primary Hostname\nP2 - Status Primary Hostname\nS1 - Streaming Secondary Hostname\nS2 - Status Secondary Hostname";
	}

	// select type of report
	public setReport(type) {

		let userReportTimezone = localStorage.getItem('USER_TIMEZONE_NAME');

		this.service.downloadSecurecxReport(type, this.filters, userReportTimezone).subscribe((data) => {
			let filename = null;
			let currentDate = new Date();
			let month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
			let date = ("0" + currentDate.getDate()).slice(-2);
			let fullDateFormat = currentDate.getFullYear() + month + date;
		
			filename = `securecx_report_${fullDateFormat}.xlsx`;

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
}
