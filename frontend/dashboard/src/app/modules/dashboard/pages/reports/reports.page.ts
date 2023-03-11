import { Component, OnInit, ElementRef, ViewChild } from '@angular/core';
import { UserService, WorkstationService } from '@app/services';
import {COMMA, ENTER} from '@angular/cdk/keycodes';
import {FormGroup, FormControl, Validators} from '@angular/forms';
import { Observable } from 'rxjs';
import {map, startWith} from 'rxjs/operators';
import {MatChipInputEvent} from '@angular/material/chips';
import {MatAutocompleteSelectedEvent} from '@angular/material/autocomplete';
import { DatePipe } from '@angular/common';

import swal from 'sweetalert2';

export interface IAccounts {
    name: string;
  }
  
@Component({
    selector: 'reports',
    templateUrl: './reports.page.html'
})
export class ReportsPage implements OnInit {

    public reportForm = new FormGroup({
        reportOption : new FormControl('detailed'),
        reportType : new FormControl(''),
        reportTypes : new FormControl('',[Validators.required]),
        startDate : new FormControl('', [Validators.required]),
        endDate : new FormControl('', [Validators.required]),
        selectedAccount : new FormControl(),
        locationSelect : new FormControl(''),
        selectedConnection : new FormControl(''),
        thresholdSelect : new FormControl(''),
        selectedThreshold : new FormControl(''),
        selectedAgent : new FormControl(''),
        selectedOption : new FormControl(''),
        selectedVLAN : new FormControl(''),
    })



    filteredReports: Observable<string[]>;
    reportType: any = [];
    @ViewChild('reportInput') reportInput: ElementRef<HTMLInputElement>;
    reports: string[] = [];

    selectedAccount: string[] =[];
    accounts:string[] = []

    locationSelect : string[];
    locations : string[] = []

    thresholdSelect : any = [];
    thresholds : string[] = []

    filteredEmployee: Observable<string[]>;

    public account : Array<string>;
    public filter : any;
    // public locations : Array<string>;
    response :any;

    public downloaded : boolean = false;

    reportText : any = [];

    public utilsData = ['CPU', 'RAM', 'RAM Usage', 'Disk', 'Free Disk'];


    constructor( 
        private WorkstationService : WorkstationService,
        private UserService : UserService,
        public datepipe : DatePipe
    ) { }

    ngOnInit(): void {
        
        this._getReportType();
        this._getAccounts();
    }
    

  _getReportType()
  {
    const self = this;
    this.WorkstationService.getReportType().subscribe((response) => {
      this.reportType = response;
    })
  }

  _getAccounts(){
      const self = this;
      this.WorkstationService.getConnectionFilters().subscribe((response) => {
          self.filter = response
          self.selectedAccount = self.filter.account
          // self.locationSelect = self.filter.location
          // console.log(self.accounts)
          // console.log(self.locationSelect)
      })
  }

  downloadReport(form){
    this.downloaded = true;
    this.reportForm.controls['reportTypes'].setValue(this.reports);
    this.reportForm.controls['selectedAccount'].setValue(this.account)
    this.reportForm.controls['locationSelect'].setValue(this.locations)
    this.reportForm.controls['selectedThreshold'].setValue(this.thresholds)

    if( this.reportForm.invalid )
          return;

    swal.fire({
      title: `Report Generation`,
      html:'<h6 id="swalInfo">SentryCX is Checking Database Record...</h6>',
      icon: 'info',
      showCancelButton: false,
      showConfirmButton: false,
      allowOutsideClick : false,
      didOpen: () => {
        swal.showLoading()
      }
    })


    if(this.thresholds.length != 0 && this.reportForm.controls['selectedOption'].value==="")
    {
      alert('You have chosen a threshold therefore select Option is required')
    }else{
      
          this.WorkstationService.DownloadReportDetails(form.value).subscribe((data) => {
              if(data.type == 'application/json')
              {
                swal.fire({
                  title: `Report Generation`,
                  text : 'No record is found',
                  icon: 'info',
                  showCancelButton: false,
                })
              }else{

                document.getElementById('swalInfo').innerHTML = 'SentryCX is Exporting Data to Excel...';
                let filename = null;
                let currentDate = new Date();
                let startDate = this.datepipe.transform(this.reportForm.controls['startDate'].value, 'MMM dd, yyyy');
                let endDate = this.datepipe.transform(this.reportForm.controls['endDate'].value, 'MMM dd, yyyy');
                let date = ("0" + currentDate.getDate()).slice(-2);
                // let fullDateFormat = currentDate.getFullYear() + month + date;
                let fullDateFormat = startDate+' - '+endDate;
                if(startDate==null)
                {
                  fullDateFormat = 'All';
                }
        
                filename = `sentrycx_${this.reportText}_${fullDateFormat}.xlsx`;
        
                let blob = new Blob([data], {
                    type: 'application/octet-stream',
                });
                if (typeof window.navigator.msSaveBlob !== 'undefined') {
                    window.navigator.msSaveBlob(blob, filename);
                    
                } else {
                    let blobURL = window.URL.createObjectURL(blob);
                    let tempLink = document.createElement('a');
                    tempLink.style.display = 'none';
                    tempLink.href = blobURL;
                    tempLink.download = filename;
                    tempLink.click();
                    window.URL.revokeObjectURL(blobURL);
                    setTimeout(this.closeSwal, 500);
                    
                }
              }
              
          })
      
    }
  }
  closeSwal()
  {
    swal.close()
  }


  get rf(){
    return this.reportForm.controls;
  }

  onReportChange(report)
  {

      const self = this;
      this.reportText = []
      this.reports = report.map(function(obj){
        return obj.id
      })
      for(let rep in report)
      {
          this.reportText.push(report[rep].type);
      }
      
      this.getThreshold(this.reports);

  }
  

  // for Accounts

  onAccountChange(account)
  {
    this.account = account;
    this.locationSelect = []
    this.getLocationPerAccount();
    
  }

  getThreshold(code, account=null)
  {
    const self = this;
    this.WorkstationService.getThreshold(code, account).subscribe(resp => {
      self.thresholdSelect = resp;
    })
  }

  getLocationPerAccount()
  {
    const self = this
    this.WorkstationService.getLocationByAccount(this.account).subscribe(response => {
      self.response = response

      self.locationSelect = self.response.map(function(obj){
        return obj.location
      })
      console.log(self.locationSelect)

    })
    
    this.getThreshold(this.reports, this.account)
  }

  // for location

  onLocationChange(location)
  {
    this.locations = location;
    console.log(location)
  }
  // for threshold

  onThresholdChange(threshold)
  {
    if(this.reportForm.controls['selectedOption'].value != "")
    {
      this.thresholds = threshold.map(function(obj){
        return obj.id
      })
    }else{
      alert('You must select an option')
      this.reportForm.controls['thresholdSelect'].setValue('')
      return
    }
    
  }



}