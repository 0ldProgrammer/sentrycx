import { Component, OnInit } from '@angular/core';
import { TriggerService, AuthService } from '@app/services';
import { IPaginate } from '@app/interfaces/index';
import { PaginateFactory } from '@app/factory/index';
import { FormControl} from '@angular/forms';
import swal from 'sweetalert2';
import { Router } from '@angular/router';
import { identifierModuleUrl } from '@angular/compiler';

interface PotentialTriggerData {
    id:  number,
    event: string,
    triggered_by : string,
    agent_name: string,
    email : string,
    position : string,
    site : string,
    manager : string,
    execution_type: number,
}

@Component({
    selector: 'potential-triggers',
    templateUrl: './potential-triggers.page.html'
})

export class PotentialTriggersPage implements OnInit {

    public isLoading : Boolean = true;
    public pageNo = 1;
    public paginate : IPaginate = PaginateFactory.init() ;
    public triggeredData : Array<PotentialTriggerData> = [];
    public selectedData : any = [];

    filter = new FormControl('');

    constructor(
        private service : TriggerService,
        private auth : AuthService,
        private router :  Router
    ) {}

    ngOnInit(): void { 
        const self = this;
        this.loadPotentialTrigger();
        this.filter.valueChanges.subscribe(val => {
             this.searchValue(val)
        });
    }

    public clickSearch()
    {
        let value = this.filter.value;
        this.searchValue(value)
    }

    public searchValue(val)
    {
        const self = this;
        this.service.loadPotentialTrigger(this.pageNo,'agent_name', val,'1').subscribe( response => {
            self.triggeredData = response['data'] as Array<PotentialTriggerData>;
            self.paginate = response as IPaginate;
            self.paginate['perPage'] = response['per_page'];
        });
    }

    public triggerSelect(type){
        const self = this;
        this.service.loadPotentialTrigger(this.pageNo, 'execution_type', type).subscribe( response => {
            self.triggeredData = response['data'] as Array<PotentialTriggerData>;
            self.paginate = response as IPaginate;
            self.paginate['perPage'] = response['per_page'];

            console.log(self.triggeredData)
        });
    }
    

    public loadPotentialTrigger(){
        const self = this;
        this.service.loadPotentialTrigger(this.pageNo,'execution_type').subscribe( response => {
            self.triggeredData = response['data'] as Array<PotentialTriggerData>;
            self.paginate = response as IPaginate;
            self.paginate['perPage'] = response['per_page'];

            console.log(self.triggeredData)
        });
    }


   /* 
    * Change page actions
    *
    */
   public pageChanged( event ){
        this.pageNo = event['pageIndex'] + 1;
        this.loadPotentialTrigger();
    }
  

   search_value = '';
}