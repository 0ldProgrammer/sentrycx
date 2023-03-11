import { Injectable } from '@angular/core';
import { IPaginate } from '@app/interfaces';

@Injectable()
export class PaginateFactory {

  static init(){
    return {
      currentPage : 1,
      total   : 0,
      perPage : 20,
      from : 0,
      to   : 0
    } as IPaginate
  }

  static parse( rawData ) {
    return {
      currentPage : rawData['current_page'],
      total   : rawData['total'],
      perPage : rawData['per_page'],
      from : rawData['from'],
      to   : rawData['to']
    } as IPaginate
  }

}
