import { Injectable } from '@angular/core';
import io from 'socket.io-client';
import { environment } from '@env/environment';

@Injectable()
export class SocketService {
  private socket;

  private isConnected : Boolean;

  constructor(){
    // const socket = io(environment.socketEndpoint, { path : environment.socketPath });
  }

  /*
   * Connects to the socket server
   * Clears all the previous connection
   */
  public connect(){
    if( !this.isConnected )
      this.socket = io(environment.socketEndpoint, { path : environment.socketPath });

    this.clear();
    return;
  }


  /*
   * Disconnect the socket server
   */
  public disconnect(){
    this.socket.close();
  }

  /*
   * Bind s an event
   */
  public bind( eventName, callback ){
    this.socket.on(eventName, callback);
  }

   /*
   * Unbind and event
   */
  public unbind( eventName ){
    this.socket.off(eventName);
  }

  /*
   * Clears all the event
   */
  public clear(){
    this.socket.removeAllListeners();
  }
}
