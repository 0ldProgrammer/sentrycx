import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import {  UserConfigService } from '@app/services';
import { interval } from 'rxjs';

@Component({
    selector: 'auto-refresher',
    templateUrl: './auto-refresher.component.html'
})
export class AutoRefresherComponent implements OnInit {
    @Output() onChange = new EventEmitter;
    private refreshInterval; 
    /*
    * Auto Refresh toggle
    *
    */
    public autoRefreshToggler = "<i class='material-icons'>settings</i>";

    private CACHE_NAME = 'USER_AUTO_REFRESH_OPTION';

    public selectedOption = [ '300000' ];
    public selectedOptionLabel = '...';

    public autoRefreshOptions = [
        { value: 'none', name : 'Stop Auto Refresh' },
        { value: '300000', name : 'Every 5 mins.' },
        { value: '600000', name : 'Every 10 mins.' },
        { value: '1800000', name : 'Every 30 mins.' },
        { value: '3600000', name : 'Every hour' },
    ]

    constructor( private userConfig : UserConfigService ) { }

    ngOnInit(): void { 
        this._loadCache();

        this._initialInterval();
    }

    private _loadCache(){
        const cached = this.userConfig.get(this.CACHE_NAME);

        if( !cached ) return;

        this.selectedOption = cached;
    }

    private _initialInterval(){
        const intervalTime = this.selectedOption[0];

        this._startInterval( intervalTime );
    }

    private _startInterval( intervalTime ){
        const self = this;

        this._updateLabel( intervalTime );

        if( intervalTime == 'none'){
            clearInterval( this.refreshInterval);
            return;
        }

        this.refreshInterval = setInterval( () => {
            self.onChange.emit();
        }, intervalTime );
    }

    private _updateLabel( intervalTime ){
        const name = this.autoRefreshOptions.find( e => e.value == intervalTime )['name'];

        if( intervalTime == 'none' ){
            this.selectedOptionLabel = "Dashboard won't update automatically";
            return;

        }

        this.selectedOptionLabel = `Dashboard will update ${name}`;
    }

    public refreshToggle( intervalTime ){
        
        this.selectedOption = intervalTime;
        this.userConfig.set(this.CACHE_NAME, [ intervalTime ]);

        this._startInterval( intervalTime );
    }
}
