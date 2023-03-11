import { Component, Inject, OnInit } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA} from '@angular/material/dialog';

@Component({
    selector: 'playbook-excel',
    templateUrl: './playbook-excel.component.html',
    styleUrls : ['./playbook-excel.component.css']
})

export class PlaybookExcelComponent implements OnInit {
    public isLoading : Boolean = true;
    public displayStyle : String = 'none';

    
    constructor(
        public dialogRef: MatDialogRef<PlaybookExcelComponent>,
        @Inject(MAT_DIALOG_DATA) public data 
    ) { }

    ngOnInit() { }

    loaded(){
        this.isLoading = false;
        this.displayStyle = 'block';
      }
}