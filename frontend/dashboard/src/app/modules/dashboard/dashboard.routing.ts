import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AdminLayoutComponent } from '@layouts/admin/admin-layout.component';
import {
  SummaryPage,
  MosViewPage,
  ConnectedAgentsPage,
  WebMTRPage,
  ZohoPage,
  ConnectedTOCPage,
  DesktopPage,
  AuditLogsPage,
  PotentialTriggersPage,
  UnresolveIssuesPage,
  InactiveAgentsPage,
  InvalidUsernamesPage,
  WebCMDPage,
  ApplicationsViewPage,
  GeoMappingPage,
  SecurecxPage,
  ReportsPage
} from './pages';

const routes: Routes = [
  { 
    path: 'dashboard', 
    component: AdminLayoutComponent ,
    children : [
      { path: 'audit-logs', component: AuditLogsPage },
      { path: 'desktop', component: DesktopPage },
      { path: 'summary', component: SummaryPage },
      { path : "connected-agents", component : ConnectedAgentsPage },
      { path : "mtr/:id", component : WebMTRPage },
      { path : "connected-toc", component : ConnectedTOCPage },
      { path : "mos-view", component : MosViewPage },
      { path : "geo-mapping", component : GeoMappingPage },
      { path : "securecx", component : SecurecxPage },
      { path : "potential-triggers", component : PotentialTriggersPage },
      { path : "unresolve-issues", component : UnresolveIssuesPage },
      { path : "idle-agents", component : InactiveAgentsPage },
      { path : "unlisted", component : InvalidUsernamesPage },
      { path: "web-cmd/:id", component : WebCMDPage},
      { path : "applications-view", component : ApplicationsViewPage },
      { path : "reports-view", component : ReportsPage }
    ]
  },
  // TODO : If there are more integrations, move this to separate module
  {
    path: 'integrations', 
    component: AdminLayoutComponent ,
    children : [
      { path : "zoho", component : ZohoPage}
    ]
  }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class DashboardRoutingModule { }
