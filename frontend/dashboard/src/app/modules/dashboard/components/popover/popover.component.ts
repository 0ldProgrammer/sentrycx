import { Component, Input } from '@angular/core';

@Component({
    selector: 'popover',
    templateUrl: 'popover.component.html',
    styleUrls : ['./popover.component.css']
})

export class PopoverComponent   {
    @Input('data-text') text : String;
    @Input('data-header') header : String ;
    @Input('data-content') content : String;

    alignButton = 'align-right';
    popover;
    // popoverPositionX = 'after';
    // popoverPositionY = 'below';
    popoverPositionX = '100';
    popoverPositionY = '58';
    // event = 'hover';
    'event' = 'click';
    autoTicks = false;
    disabled = false;
    invert = false;
    max = 100;
    min = 0;
    showTicks = false;
    step = 1;
    thumbLabel = false;
    value = 0;
    vertical = false;
    // [mdePopoverOffsetX]="-16"
    //          [mdePopoverOffsetY]="-10"
    //          [mdePopoverArrowOffsetX]="58"
    constructor() { }

    

}