import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'mosRate'
})

export class MOSRatePipe implements PipeTransform {
    private starColorRef = {
        0 : 'text-danger',
        1 : 'text-danger',
        2 : 'text-warning',
        3 : 'text-info',
        4 : 'text-success',
        5 : 'text-success'
    }

    transform( mos : number ): any {
        if( mos < 3.6 )
            return 'text-danger';
        
        if( mos < 4.3 )
            return 'text-warning';

        return 'text-success';
    }
}