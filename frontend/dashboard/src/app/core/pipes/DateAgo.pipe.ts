import { Pipe, PipeTransform } from '@angular/core';
import * as moment from 'moment-timezone';
import * as pluralize from 'pluralize';

@Pipe({
	name: 'dateAgo',
	pure: true
})
export class DateAgoPipe implements PipeTransform {

	transform(value: any, args?: any): any {
		if (value) {
			let userTzName = localStorage.getItem('USER_TIMEZONE_NAME');
			let userTzNow = moment().tz(userTzName).format('YYYY-MM-DD HH:mm:ss');
			let userTzLocal = moment.utc(value).tz(userTzName).format('YYYY-MM-DD HH:mm:ss');

			let diffMs = Math.abs(<any>new Date(userTzNow) - <any>new Date(userTzLocal));

			var msec = diffMs;
			var hh = Math.floor(msec / 1000 / 60 / 60);
			msec -= hh * 1000 * 60 * 60;
			var mm = Math.floor(msec / 1000 / 60);
			msec -= mm * 1000 * 60;
			var ss = Math.floor(msec / 1000);

			if (hh > 0) return pluralize('hour', hh, true) + ' ago';
			if (mm > 0) return pluralize('minute', mm, true) + ' ago ';
			if (ss > 0) return pluralize('second', ss, true) + ' ago';
		}

		return pluralize('second', 0, true);
	}
}