import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { includes } from 'lodash';


interface IField {
    key : string,
    name : string,
    type : string
}

@Component({
    selector: 'popup-filter',
    templateUrl: 'popup-filter.component.html'
})

export class PopupFilterComponent implements OnInit {
    // TODO : Convert this into interface
    @Input('data-fields') fields : Array<IField> ;
    @Input('data-filters') filters : any;
    @Output() onSearch = new EventEmitter;
    @Output() onReset = new EventEmitter;
    public dynamicForm : FormGroup;
    public filterRef : Array<string> = [];
    public selectedFilter = new FormControl('');

    constructor() { }

    /* 
     * Setup the dynamic form based on provided
     */ 
    public ngOnInit() { 
        
        const self = this;
        let formGroup = {};
        this.fields.forEach( (val : IField, index) => {
            formGroup[val.key] = new FormControl('');
            self.filterRef.push( val.name );
        });
        this.dynamicForm = new FormGroup( formGroup );
    }

    /*
     * Handles the search submit form
     */ 
    public search(){
        this.onSearch.emit( {
            params : this.dynamicForm.value,
            selected : this.selectedFilter.value
        });
    }


    public forceSubmit( ref ){
        console.log("REF", ref);
    }

    /*
     * Handles the reset of form
     */
    public reset(){
        this.dynamicForm.reset();
        this.onReset.emit();
    }

    /*
   * Checks if the filter dropdown is
   * is enabled or checked from the filter list
   *
   */
  hasSelected(filter, valueName){
    return includes( filter, valueName );
  }
}