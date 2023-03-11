import { OnInit, Component } from '@angular/core';
import { FormControl } from '@angular/forms';
import * as _ from 'lodash';
import { WorkstationService, SocketService } from '@app/services';
import { ActivatedRoute } from '@angular/router';
import { IAgent } from '@app/interfaces';

@Component({
  selector : 'web-mtr-page',
  templateUrl : './web-mtr.page.html',
  styleUrls : ['./web-mtr.page.css']
})
export class WebMTRPage implements OnInit {
  public connectionID;
  public isLoading  : Boolean = true;
  public initialize : Boolean = false;
  public isDone : Boolean = false;
  public isProcessing : Boolean = false;
  public agentConnection : IAgent = { mtr_result : '<br />', processing : false };
  public host = new FormControl('');
  public hops = new FormControl('10');
  public hostSuggestions : Array<any>;

  // TODO : THIS IS SO COOL MAR BUT MOVE THIS INTO
  //      : SHARED COMPONENT
  public loadingMessage = 'Initializing...';
  public updateMessage( percentage ){
    let message = 'Initializing...';
    if( percentage >= 10 )
      message = `Processing ${percentage}%`

    if( percentage >= 90 )
      message = `Processing ${percentage}% . Finalizing and fetching the results. `

    if( percentage == 100 ){
      this.isDone = true;
      message = 'Done fetching MTR Results';
    }


    this.loadingMessage = message;

    return message;
  }


  hasSelected(filter, valueName){
    return _.includes( filter, valueName );
  }


  constructor(
    private service : WorkstationService,
    private route  : ActivatedRoute,
    private socket : SocketService
  ){}

  ngOnInit(){
    this.connectionID = this.route.snapshot.paramMap.get('id')
    this.loadData( true );
    this.listenForChanges();
  }


  sendRequest(){
    const self = this;
    const params = {
      sessionID : this.agentConnection.session_id,
      host : this.host.value,
      hops : this.hops.value,
      worker_id : this.agentConnection.worker_id
    };

    this.agentConnection.progress = 1;
    // this.service.sendRequest( this.agentConnection.session_id, this.host.value ).then( (response) => {
    this.service.sendRequest( params ).then( (response) => {
      self.isProcessing = true;
      self.isDone = false;
    }, ( error ) => {
    });
  }


  loadData( init : Boolean ){
    const self = this;
    self.isLoading = true;

    this.service.getConnection( this.connectionID ).subscribe( (data) => {
      self.host.setValue( data['data']['mtr_host'] );
      self.hostSuggestions = data['apps'];
      self.agentConnection = data['data'] as IAgent;
      self.isProcessing = self.agentConnection.processing;

       if( self.agentConnection.progress === 100 && !init )
        self.isDone = true;

        self.isLoading = false;
    });
  }

  listenForChanges(){
    const self = this;
    this.socket.connect();
    this.socket.bind('dashboard:agent-mtr-updated', (data) => {
      console.log("HERE?", data);
        if(data['data']['worker_id'] == self.agentConnection.worker_id )
        self.loadData( false );
    });
  }


}
