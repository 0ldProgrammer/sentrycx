import { Component, OnInit, ViewChild } from '@angular/core';
import { IPaginate, IAccount, IAux } from '@app/interfaces';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { DomSanitizer } from '@angular/platform-browser';
import { MaintenanceService, ToastService } from '@app/services';
import swal from 'sweetalert2';

declare const $: any;
@Component({
  selector: 'aux-list-page',
  templateUrl: 'aux-list.page.html',
  styleUrls: ['./aux-list.page.css']
})

export class AuxListPage implements OnInit {
  public paginate: IPaginate;
  public accounts: Array<IAccount> = [];
  public auxs: Array<IAux> = [];

  md_table: string = "12";
  md_add_button: string = "6";
  no_account: boolean = true;
  hide_add_edit_form: boolean = true;
  filter = new FormControl('');
  aux_desc: string = "Add";
  account: string;
  aux: string;
  AddUpdateForm = new FormGroup({
    aux: new FormControl('', Validators.required),
    options_list_id: new FormControl("0")
  });
  /*
   * Checks if the individual fields are valid
   */
  isInvalid(fieldName) {
    const fieldControl = this.AddUpdateForm.controls[fieldName];

    return (fieldControl.touched && fieldControl.invalid);
  }
  search_value = '';
  emailFormControl = new FormControl('', [
    Validators.required
  ]);

  constructor(
    private service: MaintenanceService,
    public sanitizer: DomSanitizer,
    private toast: ToastService
  ) {
    
  }
  selectedValue = 0;
  ngOnInit() {
    this.paginate = this.service.getPaginate();
    this.loadData();
    this.selectedValue = 1;
  }
  loadData() {
    const self = this;
    this.service.getAccounts().subscribe((data) => {
      self.accounts = data;
    });
    this.service.getAux(this.paginate.currentPage, this.account, this.filter.value).subscribe((data) => {
      this.paginate = this.service.getPaginate();
      self.auxs = data;
    });
  }

  clickSearch()
  {
    let val = this.search_value
    this.service.getAux(this.paginate.currentPage, this.account, val).subscribe((data) => {
      this.paginate = this.service.getPaginate();
      this.auxs = data;
    });
  }

  pageChanged(pageNo) {
    this.paginate.currentPage = pageNo.pageIndex + 1;
    this.loadData();
  }

  getValues(event) {
    const self = this;
    this.account = event.value;
    if (!this.account)
      this.no_account = true;
    else
      this.no_account = false;
    this.loadAuxListTable();
  }

  loadAuxListTable() {
    const self = this;
    this.service.getAux(this.paginate.currentPage, this.account, this.filter.value).subscribe((data) => {
      this.paginate = this.service.getPaginate();
      self.auxs = data;
    });
  }

  hide_aux_error: boolean = true;
  form_valid: boolean = true;
  @ViewChild('closebutton') closebutton;

  onSubmitAux(event) {
    this.hide_aux_error = true;
    this.form_valid = true;
    const txt_aux = this.AddUpdateForm.get('aux').value;

    if (txt_aux.trim() == "" || txt_aux.trim() == undefined) {
      this.hide_aux_error = false;
      this.form_valid = false;
    }
    if (!this.form_valid)
      return;
    this.service.addAuxList(this.AddUpdateForm.value).then((response) => {
      this.loadAuxListTable();
      this.AddUpdateForm.setValue({ 'aux': '', 'options_list_id': 0 });
      if (response){
        this.toast.showNotification('top', 'right', 'Aux Name ' + response[0]['name'] + ' already exist', 'success');
      } else {
        if (this.aux_desc == "Add")
          this.toast.showNotification('top', 'right', 'Successfully Added Aux!', 'success');
        else
          this.toast.showNotification('top', 'right', 'Successfully Updated Aux!', 'success');
      }
     
      this.aux_desc = "Add";
    }, (error) => {
      this.toast.showNotification('top', 'right', 'Encounter error upon saving! ' + error, 'warning');
    }
    );
    this.closebutton._elementRef.nativeElement.click()
  }

  onCheckboxChange(event, row) {
    const self = this;
    const params = {
      account: self.account,
      options_list_id: row.id,
      checked: event.checked
    };

    this.service.auxAssignDeleteAssignment(params).then((response) => {
      if (event.checked)
        this.toast.showNotification('top', 'right', 'Successfully Assigned Aux to ' + self.account + '!', 'success');
      else
        this.toast.showNotification('top', 'right', 'Successfully Removed Aux to ' + self.account + '!', 'success');
    }, (error) => {
      this.toast.showNotification('top', 'right', 'Encounter error upon saving! ' + error, 'warning');
    }
    );
  }

  editAux(aux) {
    this.hide_aux_error = true;
    this.addeditForm();
    this.AddUpdateForm.setValue({ 'aux': aux['name'], 'options_list_id': aux['id'] });
    this.aux_desc = "Edit";
  }

  addAux(mode) {
    this.hide_aux_error = true;
    this.aux_desc = "Add";
    this.AddUpdateForm.setValue({ 'aux': '', 'options_list_id': 0 });
    this.addeditForm();
  }

  auxID: string;
  auxOptions: string;

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
          options_list_id: row.id
        };
        
        this.service.deleteAux(params).then((response) => {
          this.loadAuxListTable();
        }, (error) => {
          this.toast.showNotification('top', 'right', 'Encounter error upon deletion of aux! ' + error, 'warning');
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

  addeditForm() {
    this.hide_add_edit_form = false;
  }

  onCancelSubmit() {
    this.hide_aux_error = true;
    this.hide_add_edit_form = true;
    this.AddUpdateForm.setValue({ 'aux': '', 'options_list_id': 0 });
    this.md_table = "12";
    this.md_add_button = "6";
  }
}
