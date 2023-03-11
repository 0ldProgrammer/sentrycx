import { OnChanges, Component, Input, Output, EventEmitter,ElementRef, ViewChild } from '@angular/core';
import {Observable} from 'rxjs';
import {map, startWith} from 'rxjs/operators';
import { FormControl } from '@angular/forms';
import {MatChipInputEvent} from '@angular/material/chips';
import {MatAutocompleteSelectedEvent, MatAutocomplete} from '@angular/material/autocomplete';

@Component({
    selector: 'location-autocomplete',
    templateUrl: './location-autocomplete.component.html'
})
export class LocationAutocompleteComponent implements OnChanges {
    @Input('data-title') title;
    @Input('data-options') options;
    @Input('data-init') initData;
    @Output() onChange = new EventEmitter;

    public filteredOptions: Observable<string[]>;

    public autoLocationCtrl = new FormControl();

    public selectedOptions = []; 


    @ViewChild('autoLocationInput') autoLocationInput: ElementRef<HTMLInputElement>;
    @ViewChild('autoLocation') matAutocomplete: MatAutocomplete;

    constructor() { }

    add( item ){}

    ngOnChanges(){
            // this.selectedOptions = this.initData

        this.filteredOptions = this.autoLocationCtrl.valueChanges.pipe(
            startWith(''),
            map((value : string | null) => this.options.slice())
        )
    }


    selected(event: MatAutocompleteSelectedEvent): void {

        this.selectedOptions.push(event.option.viewValue);
        this.autoLocationInput.nativeElement.value = '';
        this.autoLocationCtrl.setValue(null);

        this.onChange.emit( this.selectedOptions );
      }

    remove( selectedOption ): void {
        const index = this.selectedOptions.indexOf(selectedOption);
    
        if (index >= 0) 
          this.selectedOptions.splice(index, 1);
    }
}
