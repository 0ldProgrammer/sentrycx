import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AdminLayoutComponent } from '../../layouts/admin/admin-layout.component';

import {
  AccountsPage,
  CodeListPage,
  UsersListPage,
  EventApprovalsPage,
  SubnetMappingPage,
  VlanMappingPage,
  AuxListPage,
  DeploymentPage,
  ApplicationsListPage,
  VPNApprovalPage,
  ApplicationUrlsPage,
  SoftwareUpdatePage,
  // CollectionPage,
  MailNotificationPage,
  TimeFramePage
} from './pages';

const routes: Routes = [
  { 
    path: 'maintenance', 
    component: AdminLayoutComponent ,
    children : [
        { path : "codes", component : CodeListPage },
        { path : "users", component : UsersListPage },
        { path : 'accounts', component : AccountsPage },
        { path : 'event-approval', component : EventApprovalsPage },
        { path : 'subnet-mapping', component : SubnetMappingPage },
        { path : 'vlan-mapping', component : VlanMappingPage },
        { path : 'aux', component : AuxListPage },
        { path : 'deployment', component : DeploymentPage },
        { path : 'applications-list', component : ApplicationsListPage},
        { path : 'vpn-approval', component : VPNApprovalPage },
        { path : 'application-urls', component : ApplicationUrlsPage },
        { path : 'software-update', component : SoftwareUpdatePage },
        // { path : 'collection', component : CollectionPage },
        { path : 'mail-notification', component : MailNotificationPage },
        { path : 'time-frame', component : TimeFramePage }
        // { path : "maintenance/users/create", component : UsersAddPage },
        // { path : "maintenance/users/:id/edit", component : UsersEditPage },
    ]
  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class MaintenanceRoutingModule { }

