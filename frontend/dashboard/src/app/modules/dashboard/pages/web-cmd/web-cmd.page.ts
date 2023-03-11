import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { SocketService, WorkstationService,NotificationService, AuthService } from '@app/services';

interface ITerminal {
    id : number;
    command : string;
    response : string;
}

@Component({
    selector: 'web-cmd',
    templateUrl: './web-cmd.page.html',
    styleUrls : ['./web-cmd.page.css']
})
export class WebCMDPage implements OnInit {
    public CMDResults : Array<ITerminal>;
    public powershellResults : Array<ITerminal>;
    public terminalType = 'CMD';
    private workerID;
    public formGroup = new FormGroup({
        type : new FormControl('CMD'),
        command : new FormControl('', Validators.required )
    });
    public terminalTypes = [
        { value : 'CMD', name : 'Web CMD'},
        { value : 'Powershell', name : 'Web Powershell' }
    ]

    constructor(
        private workstation : WorkstationService,
        private route : ActivatedRoute,
        private notification : NotificationService,
        private socket : SocketService,
        private auth : AuthService
    ){}

    ngOnInit(): void { 
        this.workerID = this.route.snapshot.paramMap.get('id');
        this._checkAccess();
        this.loadData();
        this.autoLoadHandler();
    }

    public _checkAccess() {
        if( !this.auth.isAllowed( 'admin:web-cmd' ) )
          window.location.href = "/dashboard/summary";
      }

    loadData(){
        const self = this;
        this.workstation.getWebCMD( this.workerID ).subscribe( data => {
            self.CMDResults = data['cmd'];
            self.powershellResults = data['powershell'];
        });
    }

    updateResults( data ){
        if( data.type == 'CMD' ){
            this.CMDResults = this.CMDResults.map( (result) => {
                return ( result.id == data.id ) ? data : result;
            });
            return;
        }

        this.powershellResults = this.powershellResults.map( result => {
            return ( result.id == data.id ) ? data : result;
        });
    }

    autoLoadHandler(){
        const self = this;
        this.socket.connect();
        this.socket.bind('dashboard:web-cmd', (data) => {
            self.updateResults( data as ITerminal );
        })
    }

    onChangeCommandType( type ){
        this.terminalType = type;
        this.formGroup.controls['type'].setValue( type );
    }

    executeCommand(){
        const self = this;
        if( this.formGroup.invalid )
            return;

        this.workstation.executeCommand( this.workerID, this.formGroup.value ).then( response => {
            self._initCommand({
                id: response['data']['id'],
                command : response['data']['command'],
                response : response['data']['response']
            } as ITerminal );
            self.formGroup.controls['command'].setValue('');
            self.notification.success("Command has been sent")
        });
    }

    private _initCommand( item ){
        if( this.terminalType == 'CMD' ){
            this.CMDResults.unshift( item );
            return;
        }

        this.powershellResults.unshift( item );
    }
}
