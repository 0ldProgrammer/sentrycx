import { Component, OnInit, ViewChild } from '@angular/core';
import { TriggerService, ToastService } from '@app/services';
import { IPaginate, IVpnApproval } from '@app/interfaces';
import { FormControl, FormGroup, Validators} from '@angular/forms';
import swal from 'sweetalert2';

@Component({
    selector: 'vpn-approval',
    templateUrl: './vpn-approval.page.html'
})

export class VPNApprovalPage implements OnInit {

    public isLoading : Boolean = true;
    searchBar = new FormControl('');
    public paginate: IPaginate;
    public vpnApprovalData: Array<IVpnApproval> = [];
    public selectedData : IVpnApproval;
    public currentStatus = 'Pending';
    public setButton;

    filter = new FormControl('');

    VpnApprovalForm = new FormGroup({
      status: new FormControl('', Validators.required),
      action_taken_remarks: new FormControl('', Validators.required)
    });

    @ViewChild('closebutton') closebutton;

    constructor(
        private service : TriggerService,
        private toast: ToastService
    ) {}

    ngOnInit(): void { 
        this.paginate = this.service.getPaginate();
        this.loadVpnApprovaldata();
    }

    public searchValue()
    {
        const self = this;
        this.service.getVpnApprovalList(this.paginate.currentPage, this.paginate.perPage, this.currentStatus, this.searchBar.value).subscribe( (data) => {
            self.vpnApprovalData = data;
            self.paginate = this.service.getPaginate();
        });
    }

    public onSelectStatus(type){
        const self = this;
        this.currentStatus = type;
        this.service.getVpnApprovalList(this.paginate.currentPage, this.paginate.perPage, type, this.searchBar.value).subscribe((data) => {
            self.vpnApprovalData = data;
            self.paginate = this.service.getPaginate();
        });
    }
    

    public loadVpnApprovaldata(){
        const self = this;
        this.service.getVpnApprovalList(this.paginate.currentPage, this.paginate.perPage, this.currentStatus, this.searchBar.value).subscribe((data) => {
            self.vpnApprovalData = data;
            self.paginate = this.service.getPaginate();
        });
    }


   /* 
    * Change page actions
    *
    */
   public pageChanged(pageNo) {
    this.paginate.currentPage = pageNo.pageIndex + 1;
    this.loadVpnApprovaldata();
  }

  public setVpnStatus(data, stat) {

    this.selectedData = data;

    this.setButton = stat === 'Approved' ? 'Approve' : 'Deny';

    this.VpnApprovalForm.setValue({
        status: stat,
        action_taken_remarks: this.selectedData.action_taken_remarks
    });
  }

  public onSubmitVpnApproval()
  {
    const params = {
      options_list_id: this.selectedData.id,
      params : this.VpnApprovalForm.value
    };

    const action_taken_remarks = this.VpnApprovalForm.value.action_taken_remarks;
    const status = this.VpnApprovalForm.value.status

    if (status != 'Pending' && action_taken_remarks) { 
      this.service.updateVpnApproval(params).then((data) => {
        
        if (status == 'Approved') {
            this.toast.showNotification('top', 'right', 'VPN Access Request has been successfully Approved!', 'success');
        } else {
            this.toast.showNotification('top', 'right', 'VPN Access Request has been Denied!', 'success');
        }
          
      });

      this.loadVpnApprovaldata();

      this.VpnApprovalForm.setValue({ 'status': '', 'action_taken_remarks': '' });

      this.closebutton._elementRef.nativeElement.click();

    } else {    
      swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'field remarks is required!',
      })
    }

  }

  public searchEmptyValue()
  {
      const self = this;
      if(this.searchBar.value=="")
      {
          this.loadVpnApprovaldata();
      }
      
  }

  public emptySearch()
  {
        this.searchBar.setValue('');
        this.loadVpnApprovaldata();
  }

  public onSearchIcon() {
    this.searchValue();
  }
  
   search_value = '';
}