import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { UserService,ToastService ,NotificationService } from '@app/services';
import { FormGroup, FormControl } from '@angular/forms';
import { IUser, IPaginate } from '@app/interfaces';
import swal from 'sweetalert2';


interface Field {
    id : string;
    name : string;
}

interface automatedUsers {
    programme_msa: string;
    tagging: string;
    count_users: string;
}

interface ProgrammeMSA {
    programme_msa: string;
    id: number;
}

@Component({
    selector: 'job-family',
    templateUrl: './job-family.component.html'
})
export class JobFamilyComponent implements OnInit {

    public userGroupForm = new FormGroup({
        auto_add : new FormControl(false)
      });

    public HISTORICAL_FIELDS : Array<Field> =  [
        {id: 'updated_at', name : 'Timestamp'},
        {id: 'programme_msa', name:'Programme MSA' },
        {id: 'tagging', name : 'Add Users'},
        {id: 'count_users', name :'Number of users' },
        {id: 'action', name : 'Action' }
    ];

    public isLoading : Boolean = true;
    public selectedMsa : String;
    public addedOrUpdatedMSA : Boolean = false;
    public initialMSA : String;
    public initialTagging = String;
    public quality = 'Checking';
    public filters : any = {};
    public groups: Array<automatedUsers> = [];
    public listOfMsa : Array<ProgrammeMSA> = [];
    searchBar = new FormControl('');
    search_value = '';
    public paginateMSA: IPaginate = this.service.getPaginateMSA();

    constructor( 
        private service: UserService,
        private toast: ToastService,
        private notification: NotificationService
    ) { }

    ngOnInit(): void { 
        this.loadMSA();
        this.listOfMSA();

        this.searchBar.valueChanges.subscribe(val => {
            this.searchValue(val)
        });
    }


  /*
   * Loads Programme MSA
   */
  public loadMSA() {
    const self = this;
    self.isLoading = true;
    self.service.getMSA(this.paginateMSA.currentPage, this.filters).subscribe((result) => {
    self.groups = self.service.getMSAData();
    self.paginateMSA = self.service.getPaginateMSA();
    self.isLoading = false;
    });
  }

  /*
   *  Handles the changing of page
   */
    public pageChanged(pageNo) {
        this.paginateMSA.currentPage = pageNo.pageIndex + 1;
        this.loadMSA();
      }

   public listOfMSA(){
    const self = this;
    self.isLoading = true;
    self.service.getUsersMSA().subscribe( response => {
        if (!this.addedOrUpdatedMSA) {
            self.initialMSA = response[0];
            self.setMSA(self.initialMSA);
        } 
        self.listOfMsa = response as Array<ProgrammeMSA>;
        self.isLoading = false;
    });
   }

    /*
     * Change the msa selected
     */
    public setMSA( programmeMsaSelected ) {
        this.isLoading = true;
        this.selectedMsa =  programmeMsaSelected.programme_msa;
        if (programmeMsaSelected.tagging) {
            this.userGroupForm.patchValue({
                auto_add : programmeMsaSelected.tagging == 'included' ? true : false
            });
        } else {
            this.userGroupForm.patchValue({
                auto_add : false
            });
        }
        this.isLoading = false;
    }

    public searchValue(val)
    {
        const self = this;
  
        self.isLoading = true;
        self.service.searchProgrammeMsa( val, this.paginateMSA.currentPage, this.filters ).subscribe( response  => {
        self.groups = response['data'] as Array<automatedUsers>;
        self.paginateMSA = self.service.getPaginateMSA();
        self.isLoading = false;
        });
    }

    public save(event) {
        let tagging;

        if (event.value['auto_add']) {
            tagging = 'included';
        } else {
            tagging = 'excluded';
        }

        swal.fire({
            title: `Confirm to add or update ${this.selectedMsa}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `Confirm`,
            cancelButtonText: 'Cancel',
            customClass:{
              confirmButton: "btn btn-success",
              cancelButton: "btn btn-danger",
            },
              buttonsStyling: false
          }).then( result => {
            if( !result.value) 
              return;
    
              const params = { tagging : tagging, programme_msa : this.selectedMsa};
              this.service.createMsaUsers(params).then((data) => {
                  this.loadMSA();
                  this.addedOrUpdatedMSA = true;
                  this.listOfMSA();
                  this.toast.showNotification('top', 'right', data['message'], 'success');
              });
      
          })
    }

    deleteMsa(row) {
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
            const self = this;
            this.service.deleteMsa(row.id).then((response) => {
                this.toast.showNotification('top', 'right', response['message'], 'success');
                self.loadMSA();
            });
            swal.fire(
                {
                title: 'Deleted!',
                text: row.programme_msa + ' has been deleted.',
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
}
