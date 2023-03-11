import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'timeConversion'
})

export class TimeConversionPipe implements PipeTransform {
    transform(value: any ): any { 
        let timeString = value;
        let H = +timeString.substr(0, 2);
        let h = H % 12 || 12;
        let ampm = (H < 12 || H === 24) ? "AM" : "PM";
        timeString = h + timeString.substr(2, 3) + ampm;

        return timeString;
    }
}