import { Component, OnInit , Input, EventEmitter, Output} from '@angular/core';
import * as _ from 'lodash';

interface DropdownOptions {
   value : string;
   name : string;
}

@Component({
    selector: 'auto-refresher-dropdown',
    templateUrl: 'auto-refresher-dropdown.component.html'
})
export class AutoRefresherDropdownComponent implements OnInit {
    @Input('data-options') options : Array<DropdownOptions> = [];
    @Input('data-type') type = 'SINGLE';
    @Input('data-title') title;
    @Input('data-default') default : Array<string> = []
    @Input('data-class') class : String = '';
    @Input('data-closeonclick') closeOnClick : boolean = true;
    @Output() onChange = new EventEmitter();
    public focusTrapEnabled = false;
    // public selected : Array<String> = [];
    public selected  = [];

    constructor() { }

    ngOnInit() {
        this.selected = this.default;

        this.focusTrapEnabled = ( this.type == 'MULTIPLE');
     }

    /*
     * Handles the checking from the list
     */
    public check( item )  {
        const checked = ( this.selected.indexOf( item ) !== -1);
        if( this.type == 'MULTIPLE' ) {
            this._checkMultiple(checked , item );
            return;
        }

        this._checkSingle( item );
    }

    /* 
     * Process the checbox with single selection setup
    */
    private _checkSingle( item ) {
        this.selected = [ item ];
        this.onChange.emit( item )
    }

    /*
     * PRocess the checkbox with multiple selection setup
     */
    private _checkMultiple( checked, item ) {
        if( !checked ) 
            this.selected = [...this.selected, item];
        else 
            this.selected = _.without( this.selected, item );
        

        this.onChange.emit( this.selected );
    }

}