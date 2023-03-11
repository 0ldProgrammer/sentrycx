import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { FormGroup, FormControl } from '@angular/forms';
import { includes, filter } from 'lodash';

interface IField {
    key : string,
    name : string,
    type : string
}

@Component({
    selector: 'sort-header-filter',
    templateUrl: 'sort-header-filter.component.html',
    styleUrls: ['./sort-header-filter.component.css']
})

export class SortHeaderFilterComponent implements OnInit {
    // added 
    // @Input('data-fields') fields : Array<IField> ;
    @Input('data-filters') filters : any;
    // 
    @Input('data-field') field : string;
    @Input('data-name') name : String;
    @Input('data-selected') selected;
    @Output() onSort = new EventEmitter;
    @Output() onSearch = new EventEmitter;
    public direction : string = 'ASC';
    public finalFilters : any;   
    public value = '';
    public dropdownSearchBar = new FormControl('');
    public search_value = '';
    public includeInArray = [];

    public result = [];

    constructor() { 
   
    }

    ngOnInit() { 
        this.dropdownSearchBar.valueChanges.subscribe(val => {
            this.searchValue(val)
        });
    }

    public searchValue(val)
    {
        const self = this;
        let temp;
        self.value = val;
        if (val) {
            temp = self.filters[self.field].filter(el => el.toUpperCase().indexOf(val.toUpperCase()) > -1);
        }

        self.result = temp;
        
    }

    sort( direction ){
        const params = {
            field : this.field,       
            direction : direction
        };
        
        this.direction = direction;
        this.onSort.emit( params );
    }    

    checkFilterData(val, event) {
        if (event.target.checked) {
            this.includeInArray.push(val);
        } else {
            this.includeInArray = this.includeInArray.filter(e => e !== val);
        }

        const paramsRef = {
            'account' : {​ account : this.includeInArray }​,
            'location' : {​ location : this.includeInArray }​,
            'ISP' : {​ ISP : this.includeInArray }​,
            'agent_name' : { agent_name : this.includeInArray },
            'VLAN' : { VLAN : this.includeInArray },
            'DNS_1' : { DNS_1 : this.includeInArray },
            'DNS_2' : { DNS_2 : this.includeInArray },
            'subnet' : { subnet : this.includeInArray }
        }
        
        this.onSearch.emit( {
            params : paramsRef[ this.field ]
        });

    }

    isChecked(item){
        
        const isChecked = this.includeInArray.filter(e => e === item);

        if (isChecked.length > 0){
            return true;
        } else {
            return false;
        }
    }

    clear(){
        this.search_value = '';
    }
    
}