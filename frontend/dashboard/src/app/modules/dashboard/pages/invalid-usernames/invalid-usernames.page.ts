import { Component, OnInit } from '@angular/core';
import * as _ from 'lodash';
import { WorkdayService } from '@app/services';
import { IPaginate, IInvalidUsername } from '@app/interfaces';
import {FormControl} from '@angular/forms';

interface IHeader {
  field : string,
  name : string,
  class : string 
}

@Component({
  selector : 'invalid-usernames-page',
  templateUrl : './invalid-usernames.page.html',
  styleUrls : ['./invalid-usernames.page.css']
})
export class InvalidUsernamesPage implements OnInit {
  public paginate : IPaginate;
  public invalidUsernames : Array<IInvalidUsername> = [];
  public mtrTooltipTitle : String ='MTR Result : ';
  public mtrTooltipContent : String = '<br />';
  public userTimezone : string = '';

  public isLoading : Boolean = false;

  public hasFiltered : Boolean = false;

  searchBar = new FormControl('');

  public invalidUsernamesHeader : Array<IHeader>  = [
    { field : 'date_triggered', name : 'Last Date initiated' , class : 'text-left' },
    { field : 'username', name : 'Username' , class : 'text-left' },
    { field : 'subnet', name : 'Subnet' , class : '' },
    { field : 'attempt', name : 'Login Attempt' , class : '' }
  ];

  public sortedField = null;

  /*
   * Handles the sorting of reported issues
   */
  public sortHeader( event ) {
    this.sortedField = event['field'];

    this.workdayService.setSort( event['field'], event['direction'] );

    this.loadData();
  }


  private popupConfig = {
    minWidth : '720px',
    minHeight : '560px'
  }

  /*
   * Constrcutor dependencies
   */
  constructor(
    private workdayService : WorkdayService
  ){}


  /*
   * Executes on loading of page
   */
  ngOnInit(){
    const self = this;

    this.paginate = this.workdayService.getPaginate();

    self.loadData();

    // this.searchBar.valueChanges.subscribe(val => {
    //     this.searchValue(val)
    // });

	this._setInitialUserTimezone();
  }

  public clickSearch()
  {
    let value = this.searchBar.value;
    this.searchValue(value);
  }

  public searchValue(val)
  {
      const self = this;

      this.isLoading = true;
  
      this.workdayService.searchInvalidUsernames(val, this.paginate.currentPage ).subscribe( (data) => {
        self.invalidUsernames = data;
        self.paginate = self.workdayService.getPaginate();
        self.isLoading = false;
      });
  }
	
	/*
	* Set user initial timezone
	* Monitor every second for user timezone changes
	*/
	private _setInitialUserTimezone() {
		let self = this;
		this.userTimezone = localStorage.getItem('USER_TIMEZONE_NAME');

		setInterval(function () {
			if (self.userTimezone != localStorage.getItem('USER_TIMEZONE_NAME')) {
				self.userTimezone = localStorage.getItem('USER_TIMEZONE_NAME');
				self.loadData();
			}
		}, 1000);
	}

  /*
   * Loads the invalid usernames by page number
   */
    public updateData( pageDetails ){
      this.paginate.currentPage = pageDetails.pageIndex + 1;
      this.paginate.perPage = pageDetails.pageSize;
  
      this.loadData();
    }

  /*
   * Loads the list of invalid usernames
   */
  public loadData(){
    const self = this;

    this.isLoading = true;

    this.workdayService.getInvalidUsernames( this.paginate.currentPage, this.paginate.perPage ).subscribe( (data) => {
      self.invalidUsernames = data;
      self.paginate = self.workdayService.getPaginate();
      self.isLoading = false;
    });
    
  }


  /*
   * Handles resetting the search
   */
  public reset(){
    this.hasFiltered = false;
    this.loadData();
  }

  search_value = '';
}
