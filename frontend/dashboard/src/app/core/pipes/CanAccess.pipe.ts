import { Pipe, PipeTransform } from '@angular/core';
import { AuthService } from '@app/services';

@Pipe({
    name: 'access'
})

export class CanAccessPipe implements PipeTransform {
    constructor(private auth : AuthService ){}

    transform(scopeName: any, type: String = 'allowed'): any {
        if( !scopeName ) 
            return type != 'not-allowed';

        if( type == 'not-allowed')
            return !this.auth.isAllowed( scopeName );
        return this.auth.isAllowed( scopeName );

    }
}