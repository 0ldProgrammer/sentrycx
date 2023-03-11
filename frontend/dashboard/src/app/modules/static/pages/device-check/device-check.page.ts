import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { WorkstationService, NotificationService} from '@app/services/index';
import * as _ from 'lodash';
import { environment } from '@env/environment';

interface IDevices {
    audiooutput : Array<string>,
    videoinput  : Array<string>,
    audioinput  : Array<string>
}

interface IDeviceStatus {
    audio : Boolean,
    video : Boolean,
    mic   : Boolean,
    remarks : any,
}

interface IDeviceDetail {
    id : string,
    type : string,
    icon : string,
    name : string
}

@Component({
    selector: 'device-check',
    templateUrl: 'device-check.page.html'
})
export class DeviceCheckPage implements OnInit {
    public noSitesToCheck : Boolean = false;
    public isSentryChromeInstalled : Boolean = true;
    public deviceList : IDevices = {
        audiooutput : [],
        videoinput : [],
        audioinput   : []
    };
    private _sitesChecked = 0;
    public checkSitesDevices : Boolean;
    public CHROME_EXTENSION_ID = environment.CHROME_EXTENSION_ID;//'fdpmhapjlnmokcbckgohgfmicbhhfpgd';

    public deviceStatus : IDeviceStatus = {
        audio : false,
        video : false,
        mic   : false,
        remarks : {
            audio : [],
            video : [],
            mic : []
        }
    };

    public completionStatus : IDeviceStatus = {
        audio : false,
        video : false,
        mic   : false,
        remarks : null
    }

    public soundCheck : Boolean = false;
    public testStarted : Boolean = false;
    public testEnded  : Boolean = false;
    public audio;

    public deviceDetails : Array<IDeviceDetail> = [
        { 
            id   : 'audiooutput',
            type : 'audio',
            icon : 'headset',
            name : 'Speakers'
        },{
            id   : 'audioinput',
            type : 'mic',
            icon : 'mic',
            name : 'Microphones',
        },{
            id   : 'videoinput',
            type : 'video',
            icon : 'camera_alt',
            name : 'Cameras',
        }
    ]

    TYPE_REF = {
        mic   : { id : 'audioinput', name : 'Microphone' },
        audio : { id : 'audiooutput', name : 'Microphone' },
        video : { id : 'videoinput', name : 'Camera' }
    };

    workerID = null;

    constructor( 
        private route : ActivatedRoute,
        private notification : NotificationService,
        private service : WorkstationService 
    ) { }

    public sitesToCheck : Array<any> = [];

    ngOnInit() {
        
        const self = this;
        
        this.audio = new Audio;
        this.audio.muted = false;

        this._checkConstraintByType('audio');
        this._checkConstraintByType('video');

        this._getDevicesByType('videoinput');
        this._getDevicesByType('audioinput');
        this._getDevicesByType('audiooutput');
        
        this.route.queryParamMap.subscribe(( query ) => {
            self.workerID = query['params']['workerID'];
            self._getSitesToCheck( self.workerID );
        });


     }

     private _getSitesToCheck( workerID ){
        const self = this;

        this.service.getMediaSites( workerID ).subscribe( ( response : Array<any> ) => {
            this.checkSitesDevices = response['data'][0].check_sites_devices;
            response['data'].forEach( ( site, index ) => {
                self.sitesToCheck = [ ...self.sitesToCheck, {
                    id  : site['id'],
                    url : site['url'],
                    mic : null,
                    camera : null
                }]
            });

            if(self.sitesToCheck.length == 0 ){
                self.noSitesToCheck = true;
                return;
            }

            self.checkSites();
        });
     }

     // TODO :: Transfer this to lower portion of code
     public checkSites(){
        const self = this;
        let sitesChecked = 0;

        this.sitesToCheck.forEach( (site) => {
            // sitesChecked += 1;
            self.getPermission(site,'microphone');
            self.getPermission(site,'camera');

        });
     }

     /*
      * Start the checking
      */
     public startTest(){
        this.testStarted = true;
        this.testEnded   = false;
        this.deviceStatus.remarks['audio'] = [];
        this.playAudio();
     }

     /* 
      * Do the retest specific per device
      *
      */
     public restartTest( type ){
        if( type == 'audio' )
            this.deviceStatus.remarks['mic'] = [];
        else 
            this.deviceStatus.remarks[type] = [];

        this._checkConstraintByType(type);
     } 


     /*
      * Confirm the sound is working
      * 
      */
     public confirmAudio( confirmed : Boolean ){
        this.soundCheck = true;
        
        this.audio.pause();
        this.audio.currentTime = 0;

        this.deviceStatus.audio = confirmed;

        if( !confirmed ) {
            this.deviceStatus.remarks['audio'].push("Please check if the cables have been plugged in properly. If you’re using your device’s built-in speaker, try increasing your speaker's volume – but if you’re using a speakers or a headset, make sure they’re plugged in and switched on.");
        }

        this._completeTest('audio');
        this.testEnded = true;
        
     }


     private _analyzeResults( type : string ){
         const typeRef = {
             mic      : { id : 'audioinput', name : 'Microphone' },
             audio : { id : 'audiooutput', name : 'Microphone' },
             video : { id : 'videoinput', name : 'Camera' }
         };

         const typeID = typeRef[type].id;
         const typeName = typeRef[type].name;

    
        if( !this.deviceList[typeID].length ){
            this.deviceStatus.remarks[type].push('Please check if the cables have been plugged in properly.');
            return
         }

        this.deviceStatus[type] = true;
     }

     /*
      * Plays the audio for testing only
      */
     public playAudio(){
        this.audio.loop = true;
        this.audio.src = "../../../../../assets/sounds/bensound-ukulele.mp3";
        
        this.audio.load();
        this.audio.play();
     }


    /*
     * Check the constraints
     * 
     */
    private _checkConstraintByType( type ){
        const self = this;
        const constraints = {
            'video' : (type == 'video'),
            'audio' : (type == 'audio')
        };

        const typeName = self.TYPE_REF[type].name;
        const deviceType = ( type === 'audio' ) ? 'mic' : type;

        navigator.mediaDevices.getUserMedia( constraints )
            .then( stream => {
                self._analyzeResults(deviceType);
                self._completeTest(deviceType);
            })
            .catch( error => {
                self.deviceStatus.remarks[deviceType].push(`${typeName} has been disabled/blocked. Please enable the ${typeName} on Chrome permission settings`) 
                self.deviceStatus[ deviceType  ] = false;
                self._completeTest(deviceType);
            });
    }
    
    
    /* 
     * Tag the device as completed the test
     * 
     */
    private _completeTest( type ){
        this.completionStatus[type] = true;

        if( !this.workerID )
            return;

        if( this.completionStatus.audio && 
            this.completionStatus.video &&
            this.completionStatus.mic
        ){
            this.service.sendMediaDeviceStats( this.workerID, this.deviceStatus );
        }
    }
    /*
     * Retrieve all the devices by type
     * 
     */
    private async _getDevicesByType( type ) {
        const allDevices = await navigator.mediaDevices.enumerateDevices();
        const selectedDevice = allDevices.filter( device => device.kind === type + '' );
        this.deviceList[type] = selectedDevice;
        
        return selectedDevice;
    }

    public debugRevert(  ){
        const CHROME_EXTENSION_ID = this.CHROME_EXTENSION_ID;
        window['chrome'].runtime.sendMessage(CHROME_EXTENSION_ID, {
            function : 'clearPermission',
            args : { url : 'https://emedsupport.zendesk.com/*' , type : 'microphone' }
         },
        function(response) { 
            console.log('response', response) 
        });

        window['chrome'].runtime.sendMessage(CHROME_EXTENSION_ID, {
            function : 'clearPermission',
            args : { url : 'https://emedsupport.zendesk.com/*' , type : 'camera' }
         },
        function(response) { 
            console.log('response', response) 
        });

        window['chrome'].runtime.sendMessage(CHROME_EXTENSION_ID, {
            function : 'clearPermission',
            args : { url : 'https://app.chime.aws/*' , type : 'microphone' }
         },
        function(response) { 
            console.log('response', response) 
        });

        window['chrome'].runtime.sendMessage(CHROME_EXTENSION_ID, {
            function : 'clearPermission',
            args : { url : 'https://app.chime.aws/*' , type : 'camera' }
         },
        function(response) { 
            console.log('response', response) 
        });

        this._updateSite( 3, 'microphone', 'ask' )
        this._updateSite( 3, 'camera', 'ask' )
        this._updateSite( 6, 'microphone', 'ask' )
        this._updateSite( 6, 'camera', 'ask' )
    }

    public forceAllowAll(){
        const self = this;
        this.sitesToCheck.forEach( ( site ) => {
            console.log("SITE", site);
            self.forceAllow( site );
        });

        // self.notification.success("Permission has been updated to all sites");
    }

    public forceAllow(site){
        const CHROME_EXTENSION_ID = this.CHROME_EXTENSION_ID;
        const self = this;

        self._updateSite( site['id'], 'microphone', null );
        self._updateSite( site['id'], 'camera', null );

        window['chrome'].runtime.sendMessage(CHROME_EXTENSION_ID, {
            function : 'allowPermission',
            args : { url : site['url'] , type : 'microphone' }
         },
        function(response) { });

        window['chrome'].runtime.sendMessage(CHROME_EXTENSION_ID, {
            function : 'allowPermission',
            args : { url : site['url'] , type : 'camera' }
         },
        function(response) { });

        self._updateSite( site['id'], 'microphone', 'allow' )
        self._updateSite( site['id'], 'camera', 'allow' )

        self.notification.success("Permission has been updated");
    }

    public getPermission(site, type ){
        const self = this;
        const CHROME_EXTENSION_ID = this.CHROME_EXTENSION_ID;
        window['chrome'].runtime.sendMessage(CHROME_EXTENSION_ID, {
            function : 'getPermission',
            args : { url : site['url'], type : type }
         },
        function(response) {
            console.log("RESPONSE", response);
            
            if( !response ){
                console.log("EXTENSION NOT INSTALLED : " + CHROME_EXTENSION_ID );
                self.isSentryChromeInstalled = false;
                return;
            }
            self.isSentryChromeInstalled = true;
            self._updateSite( site['id'], type, response['setting']);
        });
    }

    public _progressSiteChecking(){
        const self = this;
        this._sitesChecked++;

        if( this._sitesChecked == ( this.sitesToCheck.length * 2 ) ) {
            setTimeout(function(){
                
                self.service.sendMediaDeviceStatsPerSite( self.workerID, { remarks : self.sitesToCheck } );
            }, 5000)
            
        }
    }

    private _updateSite(id, type, setting ){
        const self = this;
        self._progressSiteChecking();
        
        let updatedSiteList = self.sitesToCheck;
        const siteIndex = _.findIndex( updatedSiteList, { id : id } );
        let updatedSite = updatedSiteList[siteIndex];
        updatedSite[type] = setting;
        updatedSiteList.splice( siteIndex, 1, updatedSite );
        self.sitesToCheck = updatedSiteList; 
    }

    public clearPermission(){
        const CHROME_EXTENSION_ID = this.CHROME_EXTENSION_ID;
        window['chrome'].runtime.sendMessage(CHROME_EXTENSION_ID, {
            function : 'clearPermission',
            args : { url : 'https://app.chime.aws/*', type : 'microphone' }
         },
        function(response) {
            console.log('response', response)
        });
    }
}