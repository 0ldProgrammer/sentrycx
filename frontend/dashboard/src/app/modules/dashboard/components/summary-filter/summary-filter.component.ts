import { Component, Input,Output,EventEmitter } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import * as _ from 'lodash';

@Component({
    selector: 'summary-filter',
    templateUrl: 'summary-filter.component.html',
    styleUrls : ['./summary-filter.component.css']
})

export class SummaryFilterComponent  {
    // TODO : Convert this into FormGroup
  filter = new FormControl();
  filterName     = new FormControl();
  filterLocation = new FormControl();
  filterCountry  = new FormControl();
  filterAccount  = new FormControl();
  filterCategory = new FormControl();
  filterStatus   = new FormControl();

  filterList: string[] = ['Account', 'Country'];

  @Output() onSearch = new EventEmitter();
  @Output() onReset  = new EventEmitter();

  @Input() filterListLocation: string[] = [];
  @Input() filterListAccount: string[]  = [];
  @Input() filterListCountry: string[]  = [];
  @Input() filterPage : Number = 1;

  filterListCategory: string[] = ['Voice', 'Application', 'Network'];
  filterListStatus: string[]   = ['Inquiry', 'Acknowledge', 'Confirmation'];

  /*
   * Defines the dependencies needed by the component
   *
   */
  constructor(){}

  /*
   * Checks if the filter dropdown is
   * is enabled or checked from the filter list
   *
   */
  hasSelected(filter, valueName){
    return _.includes( filter, valueName );
  }

 /*
  * Executes the search function
  *
  */
  search(){
    const params = {
      location   : this.filterLocation.value,
      account    : this.filterAccount.value,
      country    : this.filterCountry.value
    };

    this.onSearch.emit( params );

    //TODO : Update the connected agents filter side as well
    // const params = {
    //   page       : this.filterPage,
    //   agent_name : this.filterName.value,
    //   location   : this.filterLocation.value,
    //   account    : this.filterAccount.value
    // };

    // this.router.navigate(['/connected-agents'], { queryParams : params } )
  }

  /*
  * Executes the reset function
  *
  */
  reset(){
    this.filter.setValue('');
    this.filterLocation.setValue('');
    this.filterCountry.setValue('');
    this.filterAccount.setValue('');

    this.onReset.emit();
    console.log("RESET");
  }

  
}