import { Component, OnInit, ViewChild } from '@angular/core';
import { IPaginate, IAccount, ICode } from '@app/interfaces';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { DomSanitizer } from '@angular/platform-browser';
import { MaintenanceService, ToastService } from '@app/services';
import swal from 'sweetalert2';

declare const $: any;
@Component({
  selector: 'code-list-page',
  templateUrl: 'code-list.page.html',
  styleUrls: ['./code-list.page.css']
})

export class CodeListPage implements OnInit {
  public paginate: IPaginate;
  public accounts: Array<IAccount> = [];
  public codes: Array<ICode> = [];
  public category = new FormControl(2);

  md_table: string = "12";
  md_add_button: string = "6";
  no_account: boolean = true;
  hide_add_edit_form: boolean = true;
  filter = new FormControl('');
  code_desc: string = "Add";
  account: string;
  categoryId: string;
  code: string;
  AddUpdateForm = new FormGroup({
    code: new FormControl('', Validators.required),
    category: new FormControl(0),
    options_list_id: new FormControl("0")
  });
  /*
   * Checks if the individual fields are valid
   */
  isInvalid(fieldName) {
    console.log(fieldName);
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
  ) {}
  selectedValue = 0;
  Categories = [
    {
      id: 0,
      name: 'Select Category'
    },
    {
      id: 3,
      name: 'Application'
    }, {
      id: 2,
      name: 'Network'
    }, {
      id: 1,
      name: 'Voice'
    }
  ]
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
    this.service.getCodes(this.paginate.currentPage, this.account, this.filter.value).subscribe((data) => {
      this.paginate = this.service.getPaginate();
      self.codes = data;
    });
  }
  clickSearch()
  {
    const self = this
    this.service.getCodes(this.paginate.currentPage, this.account, this.filter.value).subscribe((data) => {
      this.paginate = this.service.getPaginate();
      self.codes = data;
    });
  }

  clear()
  {
    this.search_value = ""
    const self = this
    this.service.getCodes(this.paginate.currentPage, this.account, this.search_value).subscribe((data) => {
      this.paginate = this.service.getPaginate();
      self.codes = data;
    });
  }
  
  pageChanged(pageNo) {
    this.paginate.currentPage = pageNo.pageIndex + 1;
    this.loadData();
  }
  getValues(event) {
    console.log(event);
    const self = this;
    this.account = event.value;
    console.log(this.account);
    if (!this.account)
      this.no_account = true;
    else
      this.no_account = false;
    this.loadCodeListTable();
  }
  loadCodeListTable() {
    const self = this;
    this.service.getCodes(this.paginate.currentPage, this.account, this.filter.value).subscribe((data) => {
      this.paginate = this.service.getPaginate();
      self.codes = data;
    });
  }
  getCategoryValues(event) {
    this.categoryId = event.value;
  }

  hide_code_error: boolean = true;
  hide_category_error: boolean = true;
  form_valid: boolean = true;
  @ViewChild('closebutton') closebutton;

  onSubmitCode(event) {
    this.hide_code_error = true;
    this.hide_category_error = true;
    this.form_valid = true;
    const txt_code = this.AddUpdateForm.get('code').value;
    const category = this.AddUpdateForm.get('category').value;

    if (txt_code.trim() == "" || txt_code.trim() == undefined) {
      this.hide_code_error = false;
      this.form_valid = false;
    }
    if (category == undefined || category == "0") {
      this.hide_category_error = false;
      this.form_valid = false;
    }
    if (!this.form_valid)
      return;
    this.service.addOptionList(this.AddUpdateForm.value).then((response) => {
      this.loadCodeListTable();
      this.AddUpdateForm.setValue({ 'code': '', 'category': 0, 'options_list_id': 0 });
      if (this.code_desc == "Add")
        this.toast.showNotification('top', 'right', 'Successfully Added Code!', 'success');
      else
        this.toast.showNotification('top', 'right', 'Successfully Edited Code!', 'success');

      this.code_desc = "Add";
    }, (error) => {
      this.toast.showNotification('top', 'right', 'Encounter error upon saving! ' + error, 'warning');
    }
    );
    this.closebutton._elementRef.nativeElement.click()
  }
  onCheckboxChange(event, row) {
    console.log(event);
    const self = this;
    const params = {
      account: self.account,
      options_list_id: row.id,
      checked: event.checked
    };

    this.service.codeAssignDeleteAssignment(params).then((response) => {
      if (event.checked)
        this.toast.showNotification('top', 'right', 'Successfully Assigned Code to ' + self.account + '!', 'success');
      else
        this.toast.showNotification('top', 'right', 'Successfully Removed Code to ' + self.account + '!', 'success');
    }, (error) => {
      this.toast.showNotification('top', 'right', 'Encounter error upon saving! ' + error, 'warning');
    }
    );

  }
  editCode(code) {
    this.hide_code_error = true;
    this.hide_category_error = true;
    this.addeditForm();
    console.log(this.AddUpdateForm);
    this.AddUpdateForm.setValue({ 'code': code['options'], 'category': code['category_id'], 'options_list_id': code['id'] });
    this.code_desc = "Edit";
  }
  addCode(mode) {
    this.hide_code_error = true;
    this.hide_category_error = true;
    this.code_desc = "Add";
    this.AddUpdateForm.setValue({ 'code': '', 'category': 0, 'options_list_id': 0 });
    this.addeditForm();
  }
  codeID: string;
  codeOptions: string;
  deleteConfirm(code) {
    this.codeID = code['id'];
    this.codeOptions = code['options'];
    console.log(this.codeID);
    console.log(this.codeOptions);
  }

  deleteCodeConfirm() {
    const params = {
      options_list_id: this.codeID
    };
    this.service.deleteCode(params).then((response) => {
      this.loadCodeListTable();
      this.toast.showNotification('top', 'right', 'Successfully Deleted Code!', 'success');
    }, (error) => {
      this.toast.showNotification('top', 'right', 'Encounter error upon deletion of code! ' + error, 'warning');
    });
  }
  showSwal(row) {
    console.log(row);
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
        this.service.deleteCode(params).then((response) => {
          this.loadCodeListTable();
        }, (error) => {
          this.toast.showNotification('top', 'right', 'Encounter error upon deletion of code! ' + error, 'warning');
        });

        swal.fire(
          {
            title: 'Deleted!',
            text: row.options + ' has been deleted.',
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
    this.hide_code_error = true;
    this.hide_category_error = true;
    this.hide_add_edit_form = true;
    this.AddUpdateForm.setValue({ 'code': '', 'category': 0, 'options_list_id': 0 });
    this.md_table = "12";
    this.md_add_button = "6";
  }
}
