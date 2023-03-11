import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'extract'
})

export class ExtractPipe implements PipeTransform {
    transform(value: any, property : string, defaultValue : string ): any {
        if( !value )
            return defaultValue;

        return value[property];
    }
}