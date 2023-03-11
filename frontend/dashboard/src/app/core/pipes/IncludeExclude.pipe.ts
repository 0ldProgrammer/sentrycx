import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'includeExclude'
})

export class IncludeExcludePipe implements PipeTransform {

    transform( includeExclude: string): any {

        if( includeExclude == 'included' ) {
            return 'text-success';
        } else {
            return 'text-danger';
        }
    }
}