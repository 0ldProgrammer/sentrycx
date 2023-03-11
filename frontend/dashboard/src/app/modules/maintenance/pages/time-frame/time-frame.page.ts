import { Component, OnInit, ViewChild } from "@angular/core";
import { MaintenanceService, ToastService, NotificationService, AuthService } from "@app/services";
import { IPaginate, ITimeFrames, IAccount } from "@app/interfaces/index";
import { PaginateFactory } from "@app/factory/index";
import { FormControl, FormGroup, Validators } from '@angular/forms';


@Component({
    selector: 'time-frame',
    templateUrl: './time-frame.page.html',
    styleUrls : ['./time-frame.page.css']
})

export class TimeFramePage implements OnInit {

    public isLoading : Boolean = true;
    public pageNo = 1;
    public paginate : IPaginate = PaginateFactory.init();
    public timeFramesList : Array<ITimeFrames> = [];
    public accounts: any;
    public selectedData: ITimeFrames;
    public editMode : Boolean = false;
    public operation: any;
    filter = new FormControl('');

    AddTimeFrameForm = new FormGroup({
        account: new FormControl('', [Validators.required]),
        data_to_track: new FormControl('', [Validators.required]),
        start_date: new FormControl('', [Validators.required]),
        end_date: new FormControl('', [Validators.required])

    });

    public DATA_TO_TRACK = [
        { id: 'ping', name: 'Ping'},
        { id: 'trace', name: 'Traceroute'},
        { id: 'mtr', name: 'MTR'},
        { id: 'speedtest', name: 'Speedtest' },
        { id: 'mos', name: 'MOS' },
    ];

    @ViewChild('closebutton') closebutton;

    constructor(
        private service : MaintenanceService,
        private toast : ToastService,
        private notification : NotificationService,
        private authService: AuthService,
    ) { }

    ngOnInit(): void {
        const self = this;
        this.paginate = this.service.getPaginate();
        this.loadData();
    }

    public searchValue() 
    {
        const self = this;
        this.service.getTimeFramesList(this.paginate.currentPage, this.filter.value).subscribe((data) => {
            this.paginate = this.service.getPaginate();
            self.timeFramesList = data;
            this.isLoading = false;
        })
    }

    public searchEmptyValue()
    {
        const self = this;
        if(this.filter.value=="")
        {
            this.loadTimeFramesData();
        }
    }

    public emptySearch()
    {
        this.filter.setValue('');
        this.loadTimeFramesData();
    }

    public onSearchIcon() 
    {
        this.searchValue();
    }

    public loadData() 
    {
        this.loadAccountsData();
        this.loadTimeFramesData();
    }

    public loadTimeFramesData() 
    {
        const self = this;

        this.service.getTimeFramesList(this.paginate.currentPage, this.filter.value).subscribe((data) => {
            this.paginate = this.service.getPaginate();
            self.timeFramesList = data;
            this.isLoading = false;
        })
    }

    public loadAccountsData() 
    {
        const self = this;
        const account_access = this.authService.getUser()['account_access'];
        let account_access_arr = [];
        if(account_access) {
            account_access_arr = account_access.split(',').map((val) => {
                return {name: val};
            });
            self.accounts = account_access_arr;
            console.log(self.accounts);
        }
        else {
            this.service.getAccounts().subscribe((data) => {
                self.accounts = data;
            });
        }
    }

    isInvalid(fieldName) 
    {
        const fieldControl = this.AddTimeFrameForm.controls[fieldName];
        return (fieldControl.touched && fieldControl.invalid);
    }

    public pageChanged(pageNo) 
    {
        this.paginate.currentPage = pageNo.pageIndex + 1;
        this.loadTimeFramesData();
    }

    showTimeFrameForm(editMode, timeFrameData)
    {
        let data_array = [];
        this.editMode = editMode;
        if(this.editMode) {
            this.selectedData = timeFrameData;
            if (this.selectedData.data_to_track) {
                data_array = this.selectedData.data_to_track.split(",");
            } else {
                data_array = [];
            }

            // needed to force remove local timezone detected for selectedData.start_date and selectedData.end_date values
            // then change timezone to UTC before parsing again to local timezone
            let processed_start_date = new Date(this.selectedData.start_date).toString().slice(0, 24).concat(" UTC");
            let processed_end_date = new Date(this.selectedData.end_date).toString().slice(0, 24).concat(" UTC");

            this.AddTimeFrameForm.setValue({
                account: this.selectedData.account,
                data_to_track: data_array,
                start_date: new Date(processed_start_date),
                end_date: new Date(processed_end_date)
            });
            this.operation = 'Edit';
        } else {
            this.AddTimeFrameForm.reset();
            this.operation = 'Add';
        }
    }

    public confirmDelete(timeFrameID) 
    {
        const title = "Delete Time Frame";
        const msg = "Are you sure you want to delete the selected time frame?";
        const self = this;
        this.notification.confirm(title, msg).then(result => {
            if(!result['isConfirmed'])
                return;
            
            self.notification.success("Successfully deleted");
            self.service.deleteTimeFrame(timeFrameID).then(result => {
                self.loadTimeFramesData();
            });
        });
    }

    onSubmitTimeFrame(event) {
        const self = this;

        if(!this.editMode) {
            this.service.addTimeFrame(this.AddTimeFrameForm.value).then((data) => {
                if(data['status'] === "OK") {
                    this.toast.showNotification('top', 'right', 'Time Frame Successfully Added!', 'success');
                } else {
                    this.toast.showNotification('top', 'right', 'Please check existing time frame for account!', 'danger');
                }
                
                self.loadTimeFramesData();
            })
        } else {
            this.service.updateTimeFrame(this.AddTimeFrameForm.value, this.selectedData.id).then((data) => {
                // console.log(data);
                if(data['status'] == "OK") {
                    this.toast.showNotification('top', 'right', 'Time Frame Successfully Updated!', 'success');
                } else {
                    this.toast.showNotification('top', 'right', 'Please check existing time frame for account!', 'danger');
                }
                
                self.loadTimeFramesData();
            });
        }

        this.closebutton._elementRef.nativeElement.click();
    }

    search_value = '';

    onDataToTrackChange(data_to_track) {
        this.AddTimeFrameForm.controls['data_to_track'].setValue(data_to_track.value);
    }
}