import { Component, OnInit, Output, EventEmitter } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { MatDialogRef } from '@angular/material/dialog';

@Component({
    selector: 'popup-interval',
    templateUrl: './popup-interval.component.html'
})
export class PopupIntervalComponent implements OnInit {
    @Output() onDownload = new EventEmitter;

    public intervalForm = new FormGroup({
        date_start : new FormControl(new Date(), Validators.required),
        date_end : new FormControl(new Date(), Validators.required),
        type : new FormControl('SUMMARY', Validators.required),
    });
    constructor( private dialogRef : MatDialogRef<PopupIntervalComponent>) { }

    ngOnInit(): void { }

    close(){
        this.dialogRef.close();
    }

    download(){
        if( this.intervalForm.invalid )
            return;

        this.onDownload.emit( this.intervalForm.value );
        this.dialogRef.close();
    }
}
