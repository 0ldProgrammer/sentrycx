import { OnChanges, Component, Input, Output, EventEmitter,ElementRef, ViewChild } from '@angular/core';
import {Observable} from 'rxjs';
import {map, startWith} from 'rxjs/operators'
import { FormControl } from '@angular/forms';

@Component({
    selector: 'threshold-autocomplete',
    templateUrl: './threshold-autocomplete.component.html'
})
export class ThresholdAutocompleteComponent implements OnChanges {
    @Input('data-title') title;
    @Input('data-options') options;
    @Input('data-key') optionKey;
    @Input('data-init') initData;
    @Output() onChange = new EventEmitter;

    public filteredOptions: Observable<string[]>;

    public autocompleteCtrl = new FormControl();

    public selectedOptions = [];

    @ViewChild('thresholdInput') thresholdInput: ElementRef<HTMLInputElement>;

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
            map( value => this.options.slice())
        );
    }

    private _filter(value: any): any[] {
        return this.options.filter(details =>
            // details.threshold.toString().indexOf(value)
            console.log(details.threshold.toString().indexOf(value))
            
        )
    }

    selected( event ){
        this.selectedOptions.push(event.option.value);
        this.thresholdInput.nativeElement.value = '';
        this.autocompleteCtrl.setValue(null);
        console.log(event)
        this.onChange.emit( this.selectedOptions );
    }

    remove( selectedOption ): void {
        const index = this.selectedOptions.indexOf(selectedOption);
    
        if (index >= 0) 
          this.selectedOptions.splice(index, 1);
    }
}
