import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'replace'
})

export class ReplacePipe implements PipeTransform {
    transform(text : string, searchString : string, replace : string ): any {
        return text.replace( searchString, replace );
    }
}