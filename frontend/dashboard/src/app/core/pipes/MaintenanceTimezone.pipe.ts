import { Pipe, PipeTransform } from '@angular/core';
import * as moment from 'moment-timezone';

@Pipe({ name: 'maintenanceTimezone' })
export class MaintenanceTimezonePipe implements PipeTransform {
	transform(value: any): any {
		let userTzName = localStorage.getItem('USER_TIMEZONE_NAME');
		return moment.utc(value).tz(userTzName).format('YYYY-MM-DD h:mm:ss a');
	}
}