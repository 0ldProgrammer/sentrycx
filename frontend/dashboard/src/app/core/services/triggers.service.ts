import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '@env/environment';
import { AuthService } from './auth.services';
import { PaginateFactory } from '@app/factory';
import { IVpnApproval, IPaginate } from '@app/interfaces';
import { map } from 'rxjs/operators';

// TODO :: Move all desktop trigger functions here
@Injectable()
export class TriggerService {
    private apiURL = environment.apiURL;
    private vpnApprovalData : Array<any>;
    private paginate : IPaginate = PaginateFactory.init();

    public getPaginate(){
        return this.paginate;
    }
    
    constructor ( 
        private http : HttpClient,
        private auth : AuthService ){}


    /*
     * Trigger for AUTO MOS config
     */ 
    autoMOS( workerID ){
        const secureHeader = this.auth.getTokenHeader();

        return this.http.post(`${this.apiURL}/workstation/${workerID}/mean-opinion-score/auto`, { headers : secureHeader }).toPromise();
    }

    /*
     * Trigger for MOSRequest 
     */
    MOSRequest( workerID ){
        const secureHeader = this.auth.getTokenHeader();

        return this.http.post(`${this.apiURL}/workstation/${workerID}/mean-opinion-score/request`, { headers : secureHeader }).toPromise();
    }

    /* 
    * Fetching Potential Trigger Request
    */

    loadPotentialTrigger(pageNo, column, exec_type = "", search = ""){
        const secureHeader = this.auth.getTokenHeader();
        const params = {column : column, value : exec_type, search : search}

        return this.http.get(`${this.apiURL}/workstation/potentialTriggers?page=${pageNo}`, {params : params, headers : secureHeader });
    }

    
    getVpnApprovalList(pageNo, perPage, status, search){
        const self = this;
        const pageParams = { page : pageNo, perPage : perPage, status : status, search : search  };
        const secureHeader = this.auth.getTokenHeader();
    
        let params = Object.assign({}, pageParams );
    
        return this.http.get(`${this.apiURL}/workstation/vpn-approval`, { params : params, headers : secureHeader }).pipe(
          map( response => {
            self.vpnApprovalData = response['data'];
            self.paginate = PaginateFactory.parse(response );
            return self.vpnApprovalData as Array<IVpnApproval>;
          })
        );
    }

    updateVpnApproval(params) {
        const secureHeader = this.auth.getTokenHeader();
        return this.http.post(`${this.apiURL}/workstation/vpn-approval/update`, params, { headers : secureHeader} ).toPromise();
    }

    deleteVpnRequest(params) {
        const secureHeader = this.auth.getTokenHeader();
        return this.http.post(`${this.apiURL}/workstation/vpn-approval/delete`, params, { headers : secureHeader} ).toPromise();
    }

}