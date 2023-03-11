import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'vpnStatus'
})

export class VpnStatusPipe implements PipeTransform {

    transform( status : string ): any {
        if( status == 'Approved' ) {
            return 'text-success';
        }

        if( status == 'Denied' ) {
            return 'text-danger';
        }

        return 'text-warning';
    }
}