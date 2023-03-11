import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'contains'
})

export class ContainsPipe implements PipeTransform {
    public starColorRef = {
        0 : 'text-disabled',
        1 : 'text-danger',
        2 : 'text-warning',
        3 : 'text-info',
        4 : 'text-success',
        5 : 'text-success'
    }

    transform(array: Array<String>, value : String ): any {
        return array.indexOf( value ) > -1 ;
    }
}