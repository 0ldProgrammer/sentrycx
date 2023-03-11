import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';

@Component({
    selector: 'sort-header',
    templateUrl: 'sort-header.component.html',
    styleUrls: ['./sort-header.component.css']
})

export class SortHeaderComponent implements OnInit {
    @Input('data-field') field : String;
    @Input('data-name') name : String;
    @Input('data-selected') selected;
    @Output() onSort = new EventEmitter;
    public direction : string = 'ASC';

    constructor() { }

    ngOnInit() { }

    sort( direction ){
        const params = {
            field : this.field,
            direction : direction
        };
        
        this.direction = direction;
        this.onSort.emit( params );
    }
}