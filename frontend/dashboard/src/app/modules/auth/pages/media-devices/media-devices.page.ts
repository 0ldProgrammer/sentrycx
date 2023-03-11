import { Component, OnInit } from '@angular/core';

@Component({
    selector: 'media-devices-page',
    templateUrl: 'media-devices.page.html'
})

export class MediaDevicesPage implements OnInit {
    public devices  = {
        'video': false,
        'audio': false,
        'videoinput' : false
    }

    constructor() { }

    ngOnInit() { 
        // this.checkDevice('audio');
        // this.checkDevice('video');
        // this.checkDevice('videoinput');
        this.getConnectedDevices('videoinput').then( data => {
            console.log("VIDEO INPUT", data);
        });
        this.getConnectedDevices('audiooutput').then( data => {
            console.log("AUDIO OUTPUT", data);
        });
        this.getConnectedDevices('audioinput').then( data => {
            console.log("AUDIO INPUT", data);
        });
        
        
    }

    public checkDevice( type){
        const constraints = {
            'video': true,
            'audio': true
        }
        
        navigator.mediaDevices.getUserMedia(constraints)
            .then(stream => {
                console.log('Got MediaStream:', stream);
                // self.devices[type] = true;
            })
            .catch(error => {
                // self.devices[type] = false;
                console.error('Error accessing media devices.', error);
            });
    }

    // Fetch an array of devices of a certain type
    async  getConnectedDevices(type) {
        const devices = await navigator.mediaDevices.enumerateDevices();
        return devices.filter(device => device.kind === type)
    }
}