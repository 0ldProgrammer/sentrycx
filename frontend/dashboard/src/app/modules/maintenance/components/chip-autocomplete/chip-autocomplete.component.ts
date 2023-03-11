import { OnChanges, Component, Input, Output, EventEmitter,ElementRef, ViewChild } from '@angular/core';
import {Observable} from 'rxjs';
import {map, startWith} from 'rxjs/operators'
import { FormControl } from '@angular/forms';

@Component({
    selector: 'chip-autocomplete',
    templateUrl: './chip-autocomplete.component.html'
})
export class ChipAutocompleteComponent implements OnChanges {
    @Input('data-title') title;
    @Input('data-options') options;
    @Input('data-key') optionKey;
    @Input('data-init') initData;
    @Output() onChange = new EventEmitter;

    public filteredOptions: Observable<string[]>;

    public autocompleteCtrl = new FormControl();

    public selectedOptions = [];

    @ViewChild('autocompleteInput') autocompleteInput: ElementRef<HTMLInputElement>;

    constructor() { }

    add( item ){}

    ngOnChanges() {
        this.selectedOptions = [];

        if( this.initData != '' ) 
            this.selectedOptions = this.initData;
        
        if( !this.selectedOptions )
            this.selectedOptions = [];

        this.filteredOptions = this.autocompleteCtrl.valueChanges
            .pipe(
                startWith(''),
                map( ( value:String ) => { 
                    let queryString = value.toUpperCase();
                    return this._filter(queryString);
                })
            );
    }

    private _filter(value: string): string[] {
        const filterValue = value;
        const self = this;
        
        if( !this.options ) return;

        return this.options.filter(option => {
            return option[self.optionKey].includes(filterValue);
        });
    }

    selected( event ){
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
