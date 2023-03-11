import { OnChanges, Component, Input, Output, EventEmitter } from '@angular/core';
import { FormGroup, FormControl, Validators } from '@angular/forms';

@Component({
  selector: 'account-form-component',
  templateUrl: './account-form.component.html',
  styleUrls: ['./account-form.component.css']
})
export class AccountFormComponent implements OnChanges {
  public accountForm = new FormGroup({
    id: new FormControl(''),
    name: new FormControl('', Validators.required),
    is_active : new FormControl(false),
    need_device_check : new FormControl(false),
    need_hostfile_url : new FormControl(false),
    has_securecx : new FormControl(false),
    check_sites_devices : new FormControl(false)
  });

  public checkMediaDevice : Boolean;

  @Input('data') data: any;

  @Output() onSave: EventEmitter<string> = new EventEmitter();

  ngOnChanges() {
    if (this.data != undefined) {
      this.checkMediaDevice = this.data.need_device_check;
    }
    
    if(this.data != undefined)

      this.accountForm.patchValue({
        id : this.data['id'],
        name : this.data['name'],
        is_active : this.data['is_active'],
        need_device_check : this.data['need_device_check'],
        need_hostfile_url : this.data['need_hostfile_url'],
        has_securecx : this.data['has_securecx'],
        check_sites_devices : this.data['check_sites_devices']
      });
  }

    /*
     * Checks if the individual fields are valid
     */
    isInvalid(fieldName){
      const fieldControl = this.accountForm.controls[fieldName];

      return (fieldControl.touched && fieldControl.invalid);
    }

    /*
     * Execues the bind submit function
     */
    submit(form){
      if (this.accountForm.invalid)
        return;

      this.onSave.emit(form.value);

      if (!this.accountForm.controls['id'].value)
        this.accountForm.reset();
    }

    public checkDevice(event){
      this.checkMediaDevice = event.target.checked;
    }
  /*
   * Handles reset of the form
   */
  public clear() {
    console.log("CLEAR");
    this.accountForm.reset();
  }
}
