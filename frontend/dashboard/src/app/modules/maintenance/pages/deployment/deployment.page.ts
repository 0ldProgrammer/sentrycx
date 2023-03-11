import { Component, OnInit, ViewChild } from '@angular/core';
import { MaintenanceService, ToastService } from '@app/services';
import { IPaginate } from '@app/interfaces';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import swal from 'sweetalert2';
import { fromEventPattern } from 'rxjs';


@Component({
  selector: 'deployment-page',
  templateUrl: 'deployment.page.html',
  styleUrls: ['./deployment.page.css']
})

export class DeploymentPage implements OnInit {

  searchBar = new FormControl('');
	search_value = '';
  public isCheckPSScript: boolean;
  public isCheckSelfinstall: boolean;
  public isDisabled: boolean;
  public tempData;
  public tempFilename;
  public desktopApplications;
  public paginate: IPaginate;
  public isLoading: Boolean = true;
  hide_application_error: boolean = true;
  hide_add_edit_form: boolean = true;
  app_desc: string = "Add";
  filter = new FormControl('');
  fileToUpload: File | null = null;

  AddUpdateApplicationForm = new FormGroup({
    name: new FormControl('', Validators.required),
    description: new FormControl(''),
    is_self_install: new FormControl(''),
    is_ps: new FormControl(''),
    ps_or_dl: new FormControl(''),
    arguments: new FormControl('', Validators.required),
    id: new FormControl("0"),
    execution_date: new FormControl('',Validators.required),
    // time: new FormControl('',Validators.required),
    fileUpload: new FormControl('')
  });

  constructor(
    private service: MaintenanceService,
    private toast: ToastService
  ) {
   
  }

  ngOnInit() {
    this.paginate = this.service.getPaginate();
    this.loadData();
  }

  form_valid: boolean = true;
  @ViewChild('closebutton') closebutton;

  public searchValue()
	{
    const self = this;
    this.service.getDesktopApplications(this.searchBar.value).subscribe((data) => {
      self.desktopApplications = data;
      this.paginate = this.service.getPaginate();
    });
	}

  public loadData() {
    const self = this;
    this.service.getDesktopApplications(this.searchBar.value).subscribe((data) => {
      self.desktopApplications = data;
      this.paginate = this.service.getPaginate();
    });
  }

  onSubmitApplication(e){

    const name = this.AddUpdateApplicationForm.get('name').value;
    const is_self_install = this.AddUpdateApplicationForm.get('is_self_install').value;
    const is_ps = this.AddUpdateApplicationForm.get('is_ps').value;
    
    if (name.trim() == "" || name.trim() == undefined) {
      this.hide_application_error = false;
      this.form_valid = false;
    }

    if (!this.form_valid) {
      return;
    }

    if (!is_self_install && !is_ps) {
      swal.fire(
        'Error',
        'Please select an execution type!',
        'error'
      )
    } else if (!this.AddUpdateApplicationForm.get('fileUpload').value && !this.tempFilename) { 
      swal.fire(
        'Error',
        'Please Upload a File/Installer',
        'error'
      )
    } else {
      this.isLoading = true;
      this.service.addEditApplicationData(this.AddUpdateApplicationForm.value, this.fileToUpload).then((response) => {
        this.AddUpdateApplicationForm.setValue({ 'name': '', 'description': '', 'is_self_install': 0, 'is_ps': 0, 'ps_or_dl': '', 'arguments': '', 'execution_date': '', 'id': 0, /*'time': '',*/ 'fileUpload': '' });
        if (this.app_desc == "Add")
          this.toast.showNotification('top', 'right', 'Successfully Added Application!', 'success');
        else
          this.toast.showNotification('top', 'right', 'Successfully Edited Application!', 'success');
  
        this.app_desc = "Add";
        this.isLoading = false;
        this.loadData();
        this.closebutton._elementRef.nativeElement.click();

      }, (error) => {
        this.toast.showNotification('top', 'right', 'Encounter error upon saving! ' + error, 'warning');
        this.closebutton._elementRef.nativeElement.click();
      }
      );
    }
  }

  public onFileChange(files: FileList)
  {
    if (files.length > 0) {
      let tempFile = files.item(0);
      const extensionType = tempFile.name.split('.').pop();
      if ( extensionType == 'exe' || extensionType == 'msi' || extensionType == 'zip' || extensionType == '7z') {
        this.fileToUpload = files.item(0);
      } else {
        swal.fire({
          title: 'Wrong File Extension',
          text: "Only Accepts .exe, .msi, .zip and .7z file types!",
          icon: 'error',
        });

        this.AddUpdateApplicationForm.controls['fileUpload'].reset()
        this.fileToUpload = null;
      }
    } else {
      this.fileToUpload = null;
    }

  }

  editApplication(app) {
    this.tempData = app;
    this.tempFilename = app['filename'];
    
    this.hide_application_error = true;
    this.addEditForm();
    this.AddUpdateApplicationForm.setValue({ 'name': app['name'], 'description': app['description'], 'is_self_install': app['is_self_install'], 'is_ps': app['is_ps'], 'ps_or_dl': app['ps_or_dl'], 'arguments': app['arguments'], 'execution_date': app['execution_date'], 'id': app['id'], /*'time': app['time'],*/ 'fileUpload': '' });
    this.app_desc = "Edit";
    this.checkData(app['is_self_install'], app['is_ps']);
    this.fileToUpload = null;
    this.isLoading = false;
  }
  addApplication(mode) {
    this.hide_application_error = true;
    this.tempFilename = '';
    this.app_desc = "Add";
    this.AddUpdateApplicationForm.setValue({ 'name': '', 'description': '', 'is_self_install': 0, 'is_ps': 0, 'ps_or_dl': '', 'arguments': '', 'execution_date': '', 'id': 0, /*'time': '',*/ 'fileUpload': '' });
    this.addEditForm();
    this.AddUpdateApplicationForm.controls['ps_or_dl'].disable();
    this.fileToUpload = null;
    this.isLoading = false;
  }

  addEditForm() {
    this.hide_add_edit_form = false;
  }

  onCancelSubmit() {
    this.hide_application_error = true;
    this.hide_add_edit_form = true;
    this.AddUpdateApplicationForm.setValue({ 'name': '', 'description': '', 'is_self_install': 0, 'is_ps': 0, 'ps_or_dl': '', 'arguments': '', 'execution_date': '', 'id': 0, /*'time': '',*/ 'fileUpload': '' });
  }

  showSwal(row) {
    swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger',
      },
      confirmButtonText: 'Yes, delete it!',
      buttonsStyling: false
    }).then((result) => {
      if (result.value) {
        const params = {
          id: row.id
        };
        this.service.deleteDeploymentApplication(params).then((response) => {
          this.loadData();
        }, (error) => {
          this.toast.showNotification('top', 'right', 'Encounter error upon deletion of application! ' + error, 'warning');
        });

        swal.fire(
          {
            title: 'Deleted!',
            text: row.name + ' has been deleted.',
            icon: 'success',
            customClass: {
              confirmButton: "btn btn-success",
            },
            buttonsStyling: false
          }
        )
      }
    })
  }

  /*
	 * Loads the application by page number
	 */
  pageChanged(pageNo) {
    this.paginate.currentPage = pageNo.pageIndex + 1;
    this.loadData();
  }

  checkSelfInstall(e) {
    if (e.checked) {
      this.isCheckSelfinstall = true;
    } else {
      this.isCheckSelfinstall = false;
    }
  };

  checkPSScript(e){
    if (e.checked) {
      this.isCheckPSScript = true;
      this.disabledPSTextarea(false);
    } else {
      this.isCheckPSScript = false;
      this.disabledPSTextarea(true);
    }
  }

  checkData (isSelfInstall, isPS) {
    if (isPS && !isSelfInstall) {
      this.isCheckPSScript = true;
      this.AddUpdateApplicationForm.controls['ps_or_dl'].enable();
    } else if (isPS && isSelfInstall){
      this.isCheckPSScript = true;
      this.isCheckSelfinstall = true;
      this.AddUpdateApplicationForm.controls['ps_or_dl'].enable();
    } else {
      this.isCheckSelfinstall = true;
      this.AddUpdateApplicationForm.controls['ps_or_dl'].disable();
    }
  }

  public disabledPSTextarea(isDisabled)
  {
    if (isDisabled) {
      this.AddUpdateApplicationForm.controls['ps_or_dl'].disable();
      this.AddUpdateApplicationForm.controls['ps_or_dl'].reset()
    } else {
      this.AddUpdateApplicationForm.controls['ps_or_dl'].enable();
    }

  }

  public searchEmptyValue()
  {
      const self = this;
      if(this.searchBar.value=="")
      {
          this.loadData();
      }
      
  }

  public emptySearch()
  {
      this.searchBar.setValue('');
      this.loadData();
  }

  public onSearchIcon() {
    this.paginate.currentPage = 1;
    this.loadData();
  }
}
