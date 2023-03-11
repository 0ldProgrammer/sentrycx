import { Component, Input, Output, EventEmitter } from '@angular/core';
// TODO : Change this to popover-workstation
@Component({
    selector: 'popover-workstation',
    templateUrl: 'popover-workstation.component.html'
})

export class PopoverWorkstationComponent   {
    @Input('data-agent') agent : any;
    @Input('data-agent-name') agentName : String;
    @Input('data-profile') hardwareInfo : any;
    @Input('data-text') text : String;
    @Input('data-header') header : String ;
    @Input('data-content') content : String;
    @Output() onClick = new EventEmitter; 

    alignButton = 'align-right';
    // popover;
    // popoverPositionX = 'after';
    // popoverPositionY = 'below';
    popoverPositionX = -30;
    popoverPositionY = -10;
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

    clickHandle(agent){
        // this.popover.re
        
        this.onClick.emit( agent );
    }

}