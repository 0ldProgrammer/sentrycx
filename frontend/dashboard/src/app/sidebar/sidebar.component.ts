import { Component, OnInit } from '@angular/core';
import PerfectScrollbar from 'perfect-scrollbar';
import { AuthService } from '@app/services/index';
import { environment } from '@env/environment';

declare const $: any;

//Metadata
export interface RouteInfo {
    path: string;
    title: string;
    type: string;
    icontype: string;
    collapse?: string;
    children?: ChildrenItems[];
}

export interface ChildrenItems {
    path: string;
    title: string;
    ab: string;
    type?: string;
    access? : string;
}

//Menu Items
export const ROUTES: RouteInfo[] = [{
    //     path: '/home',
    //     title: 'Maps and Statistics',
    //     type: 'link',
    //     icontype: 'map'
    // },{
        path: '/dashboard',
        title: 'Dashboard',
        type: 'sub',
        icontype: 'dashboard',
        collapse : 'dashboard',
        children: [
            {path: 'summary', title: 'Summary', ab:'S', access: 'users:summary'},
            {path: 'connected-agents', title: 'Connected Agents - PC', ab:'CA', access: ''},
            // {path: 'connected-agents-mac', title: 'Connected Agents - MAC', ab:'CAM', access: ''},
            {path: 'desktop', title: 'Desktop Dashboard', ab:'D', access : ''},
            {path: 'mos-view', title: 'MOS View', ab:'MV', access: ''},
            {path: 'connected-toc', title: 'Connected ITSS', ab:'CI', access : 'admin:connected-itss'},
            {path: 'geo-mapping', title: 'Geo Mapping', ab:'GM', access:'admin:geo-mapping'},
            {path: 'securecx', title: 'SecureCX', ab:'S', access:'users:securecx'},
            {path: 'audit-logs', title: 'Audit Logs', ab:'AL', access : 'admin:audit-logs'},
            {path: 'potential-triggers', title: 'PC Lock/Wipeout', ab:'PLC', access : 'admin:lock-wipeout'},
            {path: 'unresolve-issues', title : 'Unresolve Issues', ab : 'UI', access: 'admin:unresolve-issues'},
            {path: 'idle-agents', title : 'Idle Agents', ab: 'IA', access : 'admin:idle-agents'},
            {path: 'unlisted', title : 'Unlisted', ab: 'U', access : 'admin:unlisted'},
            {path: 'applications-view', title: 'Applications View', ab: 'AV', access: ''},
            {path: 'reports-view', title: 'Reports', ab: 'RV', access: ''}
        ]
    },{
        path: '/maintenance',
        title: 'Maintenance',
        type: 'sub',
        icontype: 'build',
        collapse : 'maintenance',
        children: [
            {path: 'users', title: 'Users Management', ab:'UM', access : 'users:scope' },
            {path: 'accounts', title: 'Accounts Management', ab:'AM', access: 'users:accounts' },
            {path: 'codes', title: 'Codes Assignment', ab:'CA', access : 'users:codes'},
            {path: 'aux', title: 'Aux Management', ab:'AM'},
            {path: 'subnet-mapping', title: 'Subnet Mapping', ab:'SM', access : 'users:subnet'},
            {path: 'vlan-mapping', title: 'VLAN Mapping', ab:'VM', access : 'users:vlan'},
            {path: 'deployment', title: 'Deployment Management', ab: 'DM', access : 'users:deployment'},
            {path: 'applications-list', title: 'Applications Management', ab:'AM', access : 'users:applications-list'},
            {path: 'vpn-approval', title: 'VPN Approval', ab:'VA', access : 'admin:vpn-approval'},
            {path: 'application-urls', title: 'Applications Urls', ab:'AU', access : 'users:applications-urls'},
            {path: 'software-update', title: 'Software Update', ab:'SU', access : 'admin:software-update'},
            {path: 'collection', title:'Collection Management', ab:'CM', access : ''},
            {path: 'mail-notification', title: 'Mail Notification', ab:'MN', access : 'admin:mail-notification'},
            {path: 'time-frame', title: 'Needs-Based Timeframe', ab:'TF', access : 'admin:time-frame'}
        ]
    }
];
@Component({
    selector: 'app-sidebar-cmp',
    templateUrl: 'sidebar.component.html',
    styleUrls: ['./sidebar.component.css']
})

export class SidebarComponent implements OnInit {
    constructor( private auth : AuthService ){}
    public manualURL = `${environment.MANUAL_URL}`;

    public user;
    private secureMenuIsAdded : boolean = false;
    // public arr : any[] = ROUTES[1].children;
    public menuItems: any[];
    ps: any;
    isMobileMenu() {
        if ($(window).width() > 991) {
            return false;
        }
        return true;
    };

    ngOnInit() {
        this.menuItems = ROUTES.filter(menuItem => menuItem);
        if (window.matchMedia(`(min-width: 960px)`).matches && !this.isMac()) {
            const elemSidebar = <HTMLElement>document.querySelector('.sidebar .sidebar-wrapper');
            this.ps = new PerfectScrollbar(elemSidebar);
        }

        // console.log(this.auth.getUser() );
        this.user = this.auth.getUser();

        // const arrayOfObject = this.arr;

        // const menuExist = obj => obj.path === 'event-approval';
        
        // if(arrayOfObject.some(menuExist)==false)
        // {
        //     this.checkSecureMenu();
        // }
        
    }
   
    checkSecureMenu()
    {
        if(this.auth.isAllowed('event-approvals:page')) {   
            ROUTES[1].children.push({path: 'event-approval', title: 'Event Approval', ab:'EA',access : 'event-approvals:page'})
            this.secureMenuIsAdded = true;
        }
    }



    updatePS(): void  {
        if (window.matchMedia(`(min-width: 960px)`).matches && !this.isMac()) {
            this.ps.update();
        }
    }
    isMac(): boolean {
        let bool = false;
        if (navigator.platform.toUpperCase().indexOf('MAC') >= 0 || navigator.platform.toUpperCase().indexOf('IPAD') >= 0) {
            bool = true;
        }
        return bool;
    }

    public logout(){
        this.auth.logout();
        window.location.href = '/auth/login';
    }

    public downloadApp(){
        const userToken = this.auth.getToken();
        // window.location.href = environment.apiURL + `/auth/desktop-app-download?token=${userToken}`;
        window.location.href = 'https://sentrycx.s3.ap-southeast-1.amazonaws.com/window/Release/Setup.exe';
    }
}
