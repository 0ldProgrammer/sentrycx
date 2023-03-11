import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { BrowserModule } from '@angular/platform-browser'
import { MaintenanceRoutingModule } from './maintenance.routing';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { SharedModule } from '@shared/shared.module';
import {MatSelectModule} from '@angular/material/select';
import {MatButtonModule} from '@angular/material/button';
import { MatAutocompleteModule } from '@angular/material/autocomplete';
import { MatChipsModule } from '@angular/material/chips';
import { MatProgressBarModule } from '@angular/material/progress-bar';

import {MatInputModule} from '@angular/material/input';
import {MatTooltipModule} from '@angular/material/tooltip';
import {MatIconModule} from '@angular/material/icon';
import {MatPaginatorModule} from '@angular/material/paginator';
import {MatCheckboxModule} from '@angular/material/checkbox';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { DashboardModule } from '@modules/index';
import {
    IncludeExcludePipe,
    TimeConversionPipe,
    MaintenanceTimezonePipe,
    VpnStatusPipe
  } from '@app/pipes';

const MODULES = [
    BrowserModule,
    CommonModule,
    SharedModule,
    FormsModule,
    ReactiveFormsModule,
    MaintenanceRoutingModule,
    MatSelectModule,
    MatButtonModule,
    MatInputModule,
    MatTooltipModule,
    MatIconModule,
    MatPaginatorModule,
    MatCheckboxModule,
    MatAutocompleteModule,
    MatChipsModule,
    DashboardModule,
    MatProgressBarModule,
    MatDatepickerModule
];


import { UserFormComponent, AccountFormComponent, PopupFilterComponent, JobFamilyComponent } from './components';

const COMPONENTS = [
    UserFormComponent,
    AccountFormComponent,
    // ChipAutocompleteComponent,
    CodeListPage,
    UsersListPage,
    AccountsPage,
    EventApprovalsPage,
    SubnetMappingPage,
    VlanMappingPage,
    PopupFilterComponent,
    JobFamilyComponent,
    IncludeExcludePipe,
    AuxListPage,
    DeploymentPage,
    TimeConversionPipe,
    ApplicationsListPage,
    VPNApprovalPage,
    MaintenanceTimezonePipe,
    VpnStatusPipe,
    ApplicationUrlsPage,
    SoftwareUpdatePage,
    // CollectionPage,
    MailNotificationPage,
    TimeFramePage
]

import {
  AccountsPage,
  CodeListPage,
  EventApprovalsPage,
  UsersListPage,
  SubnetMappingPage, 
  VlanMappingPage,
  AuxListPage,
  DeploymentPage,
  ApplicationsListPage,
  VPNApprovalPage,
  ApplicationUrlsPage,
  SoftwareUpdatePage,
//   CollectionPage,
  MailNotificationPage,
  TimeFramePage
} from './pages';
import {
    MaintenanceService,
    UserService,
    AccountsService
} from '@app/services';

const SERVICES = [
    MaintenanceService,
    UserService,
    AccountsService
]

@NgModule({
    declarations: [...COMPONENTS],
    imports: [...MODULES],
    providers: [...SERVICES],
})
export class MaintenanceModule { }
