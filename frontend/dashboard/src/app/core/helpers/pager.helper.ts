import { Injectable } from '@angular/core';
import { IPaginate } from '@app/interfaces';

@Injectable()
export class PagerHelper {

  static parse( response ){
    return {
      currentPage : response['current_page'],
      total : response['total'],
      perPage : response['per_page'],
      from : response['from'],
      to : response['to']
    } as IPaginate
  }
}
