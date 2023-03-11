import { Injectable } from '@angular/core';
import swal from 'sweetalert2';
declare const $: any;

@Injectable()
export class NotificationService {
    constructor() { }
    
    public info( message ){
        this._notify('info', message);
    }

    public success( message ){
        this._notify('success', message);
    }

    public confirm( title, message ){
        return swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `Proceed`,
            cancelButtonText: 'Cancel',
            customClass:{
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger",
            },
            buttonsStyling: false
        });
    }

    public alert( title, message ){
        swal.fire({
            title: title,
            text: message,
            buttonsStyling: false,
            customClass:{
              confirmButton: "btn btn-success",
            },
            icon: "success"
        });
    }


    private _notify(type, message ){
        $.notify({
            icon: 'notifications',
            message: message
        }, {
            type: type,
            timer: 3000,
            placement: {
                from: 'top',
                align: 'right'
            },
            template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0} alert-with-icon" role="alert">' +
          		'<button mat-raised-button type="button" aria-hidden="true" class="close" data-notify="dismiss">  <i class="material-icons">close</i></button>' +
          		'<i class="material-icons" data-notify="icon">notifications</i> ' +
          		'<span data-notify="title">{1}</span> ' +
          		'<span data-notify="message">{2}</span>' +
          		'<div class="progress" data-notify="progressbar">' +
          			'<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
          		'</div>' +
          		'<a href="{3}" target="{4}" data-notify="url"></a>' +
          	'</div>'
        });
    }
}