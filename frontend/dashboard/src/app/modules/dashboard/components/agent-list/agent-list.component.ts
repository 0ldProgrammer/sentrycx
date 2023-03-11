import { 
  Component, 
  OnInit, 
  Input, 
  Output, 
  EventEmitter 
} from '@angular/core';
import { IPaginate, IAgent } from '@app/interfaces';
import { WorkstationService } from '@app/services';
import { WorkstationDetailsComponent, ApplicationMonitoringComponent,AgentProfileComponent } from '@modules/dashboard/components';
import { MatDialog } from '@angular/material/dialog';
interface IHeader {
    field : string,
    name : string,
    class : string 
}

@Component({
    selector: 'agent-list',
    templateUrl: './agent-list.component.html',
    styleUrls: ['./agent-list.component.css']
})
export class AgentListComponent implements OnInit {
    @Input('data') connectedAgents : Array<IAgent> = [];
    @Input('loader') isLoading : Boolean;
    @Input('data-selected') selectedColumns : Array<string>;
    @Input('data-headers') headers : Array<IHeader>;
    @Output('onSort') onSort = new EventEmitter;
    @Output('onChange') onChange = new EventEmitter;
    
    public sortedField = null;    
    public speedtestTitle = "<i class='material-icons'>more_horiz</i>";

    public speedtestOptions = [
      { value : 'all', name : 'All results'},
      { value : 'threshold', name : 'Low Speedtest/MOS'},
    ];

    private popupConfig = {
        minWidth : '720px',
        minHeight : '560px'
    }

    constructor(
        private dialog  : MatDialog,
        private service : WorkstationService
    ) { }

    ngOnInit(): void { }

    public onSpeedtestFilter( event ){
      this.onChange.emit( event );
    }

    /*
    * Handles the sorting of reported issues
    */
    public sortFlag( event ) {
      this.sortedField = event['field'];
        this.onSort.emit( event );
    }

   /* 
    * Load the agent profile of the selected workerID
    *
    */
    public loadAgentProfile( workerID ){
        const self = this;

        const modal = self.dialog.open( AgentProfileComponent, self.popupConfig );

        modal.componentInstance['workerID'] = workerID;
    }

    /* 
   * Load the workstation profile of the selected workerID
   *
   */
  public loadWorkstation( args ){
    const agentName = args['agent_name'];
    const workerID  = args['worker_id'];
    
    const self  = this;
    const modal = self.dialog.open( WorkstationDetailsComponent, self.popupConfig );

    let params = {
      componentInstance : modal.componentInstance, 
      agentName : agentName, 
      workerID  : workerID
    }

    
    this._bindWorkstationPopupEvents(  params );
  }
  /*
   * Bind events to workstation popup
   */
  private _bindWorkstationPopupEvents( params ){
    const self = this;
    const workerID  = params['workerID'];
    const agentName = params['agentName'];
    const componentInstance = params['componentInstance'];

    // TODO :  Move this into service call to workstation-detilas
    this.service.getAgentWorkstation( workerID ).subscribe( (data) => {
      let hardwareInfo = false;
      if( data['data'].length > 0 )
        hardwareInfo = data['data'][0];

      componentInstance['hardwareInfo'] = hardwareInfo;
      componentInstance['agentName']    = agentName;
      componentInstance['lezap']        = data['lezap'];
      componentInstance['isLoading']    = false;
      componentInstance['eventLogs']    = data['event-logs'];
      componentInstance['singleInfo']   = true;
      componentInstance['remoteSessionLogs'] = data['zoho-logs'];
      componentInstance['mediaDevices'] = data['media-devices'];
      componentInstance['speedtest']    = data['speedtest'];
      
      //self._loadPacDetails( componentInstance, hardwareInfo['pac_address']);
    });

    }
}
