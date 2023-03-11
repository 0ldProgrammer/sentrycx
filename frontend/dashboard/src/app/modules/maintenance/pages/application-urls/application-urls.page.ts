import { Component, OnInit, ViewChild } from '@angular/core';
import { IPaginate, IAccount, ICode } from '@app/interfaces';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { DomSanitizer } from '@angular/platform-browser';
import { MaintenanceService, ToastService } from '@app/services';
import swal from 'sweetalert2';

declare const $: any;
@Component({
  selector: 'application-urls',
  templateUrl: 'application-urls.page.html',
  styleUrls : ['./application-urls.page.css']
})

export class ApplicationUrlsPage implements OnInit {

  constructor(
    private service: MaintenanceService,
  ){}

  public isLoading : Boolean = true;
  public paginate: IPaginate;
  public accounts: Array<IAccount> = [];
  search_value = '';
  filter = new FormControl('');
  account: string;
  account_id : any;
  applications : any;
  app_desc : string;

  hide_code_error: boolean = true;
  hide_category_error: boolean = true;
  form_valid: boolean = true;

  selectedAccount : any;

  public AddUpdateForm = new FormGroup({
      name: new FormControl('', Validators.required),
      description: new FormControl(''),
      url: new FormControl('', Validators.required),
      ip : new FormControl(''),
      account_id : new FormControl('0'),
      account : new FormControl(),
      is_active : new FormControl(1),
      is_loaded : new FormControl(false)
  });

  ngOnInit() {
    this.paginate = this.service.getPaginate();
    this.loadData();
  }

 loadData() {
    const self = this;
    this.service.getAccounts().subscribe((data) => {
      self.accounts = data;
    });

    this.getApplications();
      
  }


  getValues(event) {
    console.log(event);
    const self = this;
    const accountdetails = event.value.split('_')
    this.account = accountdetails[1];
    this.account_id = accountdetails[0];
  }

  getApplications()
  {
    const self = this;
    this.service.getApplicationUrls(this.paginate.currentPage, this.search_value).subscribe((data) => {
      self.applications = data;
      this.isLoading = false;
    });
  }

  onCheckboxChange(event, application)
  {
    console.log(application)

    const details = {
      id : application.id,
      is_loaded : event.checked
    }
    this.service.addApplicationUrl(details).then((response) => {
        this.getApplications();
    })
  }
  

  public onSubmitApplication()
  {
    this.AddUpdateForm.patchValue({account :  this.account, account_id : this.account_id})
    this.service.addApplicationUrl(this.AddUpdateForm.value).then((response) => {
      swal.fire(
        {
          title: 'Application URL',
          text: 'Successfully Updated',
          icon: 'success',
          customClass: {
            confirmButton: "btn btn-success",
          },
          buttonsStyling: false
        }
      )
      this.getApplications();
    })
  }

  pageChanged(pageNo) {
    this.paginate.currentPage = pageNo.pageIndex + 1;
    this.getApplications();
  }

  public showAccountForm(desc, details)
  {

    this.app_desc = desc
    this.selectedAccount = details.account_id+'_'+details.account
   
    this.AddUpdateForm.addControl('id', new FormControl(details.id, Validators.required) )
    this.AddUpdateForm.patchValue(details);
  
  }


  public searchValue()
  {
      const self = this;
      this.service.getApplicationUrls(this.paginate.currentPage, this.filter.value).subscribe((data) => {
          this.paginate = this.service.getPaginate();
          self.applications = data;
          this.isLoading = false;
      });
      
  }
  
  public searchEmptyValue()
  {
      const self = this;
      if(this.filter.value=="")
      {
          this.getApplications();
      }
      
  }

  public emptySearch()
  {
      this.filter.setValue('');
      this.getApplications();
  }

  public addApplicationUrl(desc)
  {
    this.app_desc = desc;
    this.hide_code_error = true;
    this.hide_category_error = true;
  }
}