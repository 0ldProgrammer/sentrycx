import { Pipe, PipeTransform } from '@angular/core';

interface INetworkType {
    expression : string,
    type : string
}

@Pipe({
    name: 'networkType'
})

export class NetworkTypePipe implements PipeTransform {

    private IP_REGEX : Array<INetworkType> = [
        { expression : '^192.*', type : 'WAH' },
        { expression : '^10.*', type : 'B&M' }
    ];

    transform( ipAddress : string ): any {
        let networkType = '-';

        this.IP_REGEX.forEach( value => {
            if( ipAddress.match( value.expression )) {
                networkType = value.type;
                return false;
            }
        });

        return networkType;
    }
}