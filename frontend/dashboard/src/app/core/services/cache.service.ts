import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '@env/environment';
import { map,catchError } from 'rxjs/operators';
import { Observable } from 'rxjs';
import { AuthService } from '@app/services';

interface ICache {
    key: string;
    args : any;
    expiration : number;
}

@Injectable()
export class CacheService {
    private apiURL = environment.apiURL;

    constructor( 
        private http : HttpClient,
        private auth : AuthService

    ){}

    /*
     * Store the data to localstorage for caching
     *
     */
    public store( key, data ){
        const cacheData = {
            key : key,
            expiration : Date.now() + 1800000, // 30 minutes
            args : data
        };

        localStorage.setItem(key, JSON.stringify(cacheData) );
    }

    /*
     * Updates the cache by invalidating the existing 
     * then saves a new data. 
     */
    public save(name, data){
        this.invalidateUpdatedData(name);
        this.store(`CACHE_${name}`, data );
    //     this.cache.invalidateUpdatedData('CONNECTED_AGENTS');
    // this.cache.store('CACHE_CONNECTED_AGENTS', {
    //   data : this.connectedAgents,
    //   paginate : this.paginate
    // });
    }
    

    /*
     * Checks from the localStorage for cache data
     *
     */
    public get( key ){
        const cacheString = localStorage.getItem(key);

        const cacheData   = JSON.parse( cacheString ) as ICache;

        if( !cacheString ) return false

        if( Date.now() > cacheData.expiration ){
            localStorage.removeItem(key);
            return false;
        }

        return cacheData;

    }

    /*
     * Check if there are updated data prior to caching
     *
     */
    public countUpdatedData( type ){
        const cacheCountKey = `UPDATES_${type}`;
        const currentCache  = localStorage.getItem(cacheCountKey)
        let params = { server_time : '', ph_time : ''};
        const secureHeader = this.auth.getTokenHeader();
        const ENDPOINTS_REF = {
            CONNECTED_AGENTS : 'cache/connected-agents/updates',
            DESKTOP_DASHBOARD : 'cache/connected-agents/updates',
            INACTIVE_AGENTS  : 'cache/inactive-agents/updates'
        }

        const endpoint = ENDPOINTS_REF[type];

        // if( currentCache ){
        //     const cacheData = JSON.parse( currentCache );
        //     params = cacheData['cache_time'];
        //     console.log("CACHE PARAMS", params);
        // }

        // TODO : check from localStorage if TYPE has cached data
        // return this.http.get(`${this.apiURL}/cache/connected-agents/updates`, { params : params,headers : secureHeader  } ).pipe(
        return this.http.get(`${this.apiURL}/${endpoint}`, { params : params,headers : secureHeader  } ).pipe(
            map( response => {
            //   localStorage.setItem(cacheCountKey, JSON.stringify( response ) );
              return response;
            }),
            catchError( error => Observable.throw(error) )
          );;
    }

    /*
     * Update the cache counting
     *
     */
    public invalidateUpdatedData(type){
        const cacheCountKey = `UPDATES_${type}`;
        localStorage.removeItem( cacheCountKey );
        return this.countUpdatedData(type);
    }
}