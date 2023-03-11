import { OnChanges, Component, Input, Output, EventEmitter,ElementRef, ViewChild } from '@angular/core';
import {Observable} from 'rxjs';
import {map, startWith} from 'rxjs/operators';
import { FormControl } from '@angular/forms';
import {MatChipInputEvent} from '@angular/material/chips';
import {MatAutocompleteSelectedEvent} from '@angular/material/autocomplete';

@Component({
    selector: 'select-autocomplete',
    templateUrl: './select-autocomplete.component.html'
})
export class SelectAutocompleteComponent implements OnChanges {
    @Input('data-title') title;
    @Input('data-options') options;
    @Input('data-init') initData;
    @Output() onChange = new EventEmitter;

    public filteredOptions: Observable<string[]>;

    public autocompleteCtrl = new FormControl();

    public selectedOptions = []; 


    @ViewChild('autocompleteInput') autocompleteInput: ElementRef<HTMLInputElement>;

    constructor() { }

    add( item ){}

    ngOnChanges(){
        console.log(this.initData)
        if(this.initData != '')
            // this.selectedOptions = this.initData

        // this.filteredOptions = this.autocompleteCtrl.valueChanges.pipe(
        //     startWith(''),
        //     map((value : string | null) => this.options.slice())
        // )
        this.filteredOptions = this.autocompleteCtrl.valueChanges
            .pipe(
                startWith(''),
                map((value: string | null) => (value ? this._filter(value.toUpperCase()) : this.options.slice()))
                // map( ( value:String ) => { 
                //     let queryString = value.toUpperCase();
                //     return this._filter(queryString);
                // })
            );
    }

    // ngOnChanges() {
    //     this.selectedOptions = [];

    //     if( this.initData != '' ) 
    //         this.selectedOptions = this.initData;
        
    //     if( !this.selectedOptions )
    //         this.selectedOptions = [];

    //     this.filteredOptions = this.autocompleteCtrl.valueChanges
    //         .pipe(
    //             startWith(''),
    //             map( (value:String ) => { 
    //                 let queryString = value.toUpperCase();
    //                 return this._filter(queryString);
    //             })
    //         );
    // }

    // 
   
    private _filter(value: string): string[] {
        const filterValue = value;
        const self = this;
        
        if( !this.options ) return;

        return this.options.filter(option => {
            return option.toUpperCase().includes(filterValue);
        });
    }

    selected(event: MatAutocompleteSelectedEvent): void {

        this.selectedOptions.push(event.option.viewValue);
        this.autocompleteInput.nativeElement.value = '';
        this.autocompleteCtrl.setValue(null);

        this.onChange.emit( this.selectedOptions );
      }

    remove( selectedOption ): void {
        const index = this.selectedOptions.indexOf(selectedOption);
    
        if (index >= 0) 
          this.selectedOptions.splice(index, 1);
    }
}
