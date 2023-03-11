import { Component, OnInit } from '@angular/core';
import { WorkstationService,SocketService } from '@app/services';
import {FormControl} from '@angular/forms';

interface IField {
    field : string,
    name : string,
    class : string 
}

interface IStatistics {
    account?: string;
    avg_mos: number;
    connected: number;
    wired: number;
    wireless: number;
    location?: string;
    country?: string;
    bm? : number;
    wah? : number;
    vpn? : number;
}

@Component({
    selector: 'mos-view',
    templateUrl: './mos-view.page.html'
})
export class MosViewPage implements OnInit {

    searchBar = new FormControl('');
	search_value = '';

    public accountStats : Array<IStatistics> = [];
    public countryStats : Array<IStatistics> = [];
    public locationStats : Array<IStatistics> = [];
    public tier2Breakdown : any = {};
    public detailedBreakdown : any;
    
    public totalStats : IStatistics;
    public selectedAccount = null;
    public selectedCountry = null;
    public breakdownField  = 'country';
    

    public breakdownOptions = [
        { value : 'location', name : 'Location' },
        { value : 'country', name : 'Country' },
      ];

    public isLoading : Boolean = true;
    public TABLE_FIELDS : Array<IField>  = [
        { field : 'account', name : 'Account', class : 'text-left' },
        { field : 'connected', name : 'Conn. Devices', class : '' },
        { field : 'avg_mos', name : 'AVG MOS', class : '' },
        { field : 'wired', name : 'Wired', class : '' },
        { field : 'wireless', name : 'Wireless', class : '' },
        { field : 'wah', name : 'WAH', class : '' },
        { field : 'bm', name : 'B&M', class : '' },
        { field : 'vpn', name : 'VPN', class : '' },
        { field : 'byod', name : 'BYOD', class : '' },

    ];

    public reportType = [
        { value : 'excel', name: 'Export to Excel'},
        { value : 'pdf', name: 'Export to PDF'}
      ]

    public search : any;

    filter = new FormControl('');

    constructor( 
        private service : WorkstationService,
        private socket  : SocketService
    ) { }

    ngOnInit(): void{ 
        const self = this;
        // this.loadData();
        this.socket.connect();
        this.socket.bind('dashboard:agent-list', () => {
            self.loadData();
        });

        this.searchValue();
    }

    public selectAccount( account ){
        if( this.selectedAccount == account )
            account = null;
        this.selectedAccount = account;
    }

    public selectCountry( country ){
        if( this.selectedCountry == country )
            country = null
        this.selectedCountry = country;
    }

    public onChangeBreakdown( fieldName ){
        this.breakdownField = fieldName
    }

    // NOTE : This is reduntant with LoadData
    public searchValue()
    {
        const self = this;
        self.isLoading = true;
        this.service.getConnectedStats(this.searchBar.value).subscribe( response => {
            self.isLoading = false;
            self.accountStats  = response['base']['data'];
            self.locationStats = response['location'];
            self.countryStats  = response['country'];
            self.totalStats    = response['total'];
            self.detailedBreakdown = response['detailed_breakdown']
        });
        this.search = this.searchBar.value;
    }

    public loadData(){
        const self = this;
        self.isLoading = true;
        this.service.getConnectedStats(this.searchBar.value).subscribe( response => {
            self.isLoading = false;
            self.accountStats  = response['base']['data'];
            self.locationStats = response['location'];
            self.countryStats  = response['country'];
            self.totalStats    = response['total'];
            self.detailedBreakdown = response['detailed_breakdown']
        });

    }

      // select type of report
    public setReport( type ){

        this.service.downloadReportFromMOSView( type, this.search, this.breakdownField ).subscribe( (data) => {
            let filename = null;
            let currentDate = new Date();
            let month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
            let date = ("0" + currentDate.getDate()).slice(-2);
            let fullDateFormat =  currentDate.getFullYear() + month + date;
            
            if (type === 'excel') {
                filename = `sentrycx_mos_view_${fullDateFormat}.xlsx`;
            } else {
                filename = `sentrycx_mos_view_${fullDateFormat}.pdf`;
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
