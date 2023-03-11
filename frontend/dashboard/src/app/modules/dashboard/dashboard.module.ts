import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import {
  CanAccessPipe, 
  ExtractPipe, 
  ContainsPipe, 
  JsonParsePipe,
  ReplacePipe,
  NetworkTypePipe,
  MOSRatePipe,
  DefaultPipe,
  DateAgoPipe,
  TimezonePipe,
  RemoveCommaPipe
} from '@app/pipes';

// TODO : Conver this into shared material module
import { MdePopoverModule } from '@material-extended/mde';

import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatProgressBarModule } from '@angular/material/progress-bar';
import { MatChipsModule } from '@angular/material/chips';
import { MatDialogModule } from '@angular/material/dialog';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatAutocompleteModule } from '@angular/material/autocomplete';
import { MatCardModule} from '@angular/material/card';
import { MatTabsModule } from '@angular/material/tabs';
import {MatMenuModule} from '@angular/material/menu';
import {MatIconModule} from '@angular/material/icon';
import {MatListModule} from '@angular/material/list';
import {MatSidenavModule} from '@angular/material/sidenav';
import { MatExpansionModule } from '@angular/material/expansion';

import {MatProgressSpinnerModule} from '@angular/material/progress-spinner';
import {MatBadgeModule} from '@angular/material/badge';
import {MatPaginatorModule} from '@angular/material/paginator';

import { DashboardRoutingModule } from './dashboard.routing';
import { MatTooltipModule } from '@angular/material/tooltip';
// import { MaintenanceModule } from '@modules/index';
import {
  ConnectedAgentsPage,
  WebMTRPage,
  ZohoPage,
  SummaryPage,
  ConnectedTOCPage,
  MosViewPage,
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
import {
  PlaybookExcelComponent,
  SummaryFilterComponent,
  WorkstationDetailsComponent,
  PopoverComponent,
  PopoverWorkstationComponent,
  ApplicationMonitoringComponent,
  WorkstationDetailsProfileComponent,
  PopupFilterComponent,
  DropdownComponent,
  SortHeaderComponent,
  MeanOpinionScoreComponent,
  HostfileComponent,
  AgentProfileComponent,
  HostfilePerWorkstationComponent,
  PopoverAgentComponent,
  HistoricalWorkstationProfileComponent,
  AgentProfileStandaloneComponent,
  HistoricalSpeedtestComponent,
  AutoRefresherComponent,
  SecureCXMonitoringComponent,
  PopoverRedDotComponent,
  AgentListComponent,
  PingComponent,
  TraceComponent,
  AutoRefresherDropdownComponent,
  WorkstationLogsComponent,
  SortHeaderFilterComponent,
  PopupIntervalComponent,
  ChartsGraphComponent,
  DynamicColumnsDrawerComponent,
  WebMTRComponent,
  NewWorkstationProfileComponent,
  MOSDetailsComponent,
  MOSChartsGridComponent,
  SpeedtestDetailsComponent,
  SpeedtestChartsGridComponent,
  WorkstationChartsGridComponent,
  SecureCXMonitoringGridComponent,
  OneViewGraphComponent
} from './components';

import { ChipAutocompleteComponent } from '../maintenance/components/chip-autocomplete/chip-autocomplete.component';
import { SelectAutocompleteComponent } from '../../shared/select-autocomplete/select-autocomplete.component';
import { LocationAutocompleteComponent } from './components/location-autocomplete/location-autocomplete.component';
import { ThresholdAutocompleteComponent } from './components/threshold-autocomplete/threshold-autocomplete.component';
import { ReportTypeAutocompleteComponent } from './components/report-type-autocomplete/report-type-autocomplete.component';


import {
  DashboardService,
  FlagsService,
  WorkstationService,
  SocketService,
  ZohoService,
  NotificationService,
  EventLogsService,
  AccountsService,
  WorkdayService,
  HistoricalService,
  TriggerService,
  UserConfigService,
  CacheService
} from '@app/services';
import { FlagsHelper, ToastHelper } from '@app/helpers';
import { PaginateFactory } from '@app/factory';
import { MatRadioButton } from '@angular/material/radio';

@NgModule({
  declarations: [
    SummaryFilterComponent,
    PlaybookExcelComponent,
    WorkstationDetailsComponent,
    SummaryPage,
    MosViewPage,
    PopoverComponent,
    PopoverWorkstationComponent,
    ApplicationMonitoringComponent,
    WorkstationDetailsProfileComponent,
    PopupFilterComponent,
    DropdownComponent,
    SortHeaderComponent,
    MeanOpinionScoreComponent,
    HostfileComponent,
    AgentProfileComponent,
    HostfilePerWorkstationComponent,
    PopoverAgentComponent,
    HistoricalWorkstationProfileComponent,
    AgentProfileStandaloneComponent,
    HistoricalSpeedtestComponent,
    AutoRefresherComponent,
    PotentialTriggersPage,
    ConnectedAgentsPage,
    WebMTRPage,
    ZohoPage,
    ConnectedTOCPage,
    DesktopPage,
    AuditLogsPage,
    UnresolveIssuesPage,
    InactiveAgentsPage,
    InvalidUsernamesPage,
    WebCMDPage,
    ApplicationsViewPage,
    GeoMappingPage,
    SecurecxPage,
    ReportsPage,
    SecureCXMonitoringComponent,
    PopoverRedDotComponent,
    AgentListComponent,
    PingComponent,
    TraceComponent,
    AutoRefresherDropdownComponent,
    WorkstationLogsComponent,
    SortHeaderFilterComponent,
    PopupIntervalComponent,
    ChartsGraphComponent,
    DynamicColumnsDrawerComponent,
    WebMTRComponent,
    NewWorkstationProfileComponent,
    MOSDetailsComponent,
    MOSChartsGridComponent,
    SpeedtestDetailsComponent,
    SpeedtestChartsGridComponent,
    WorkstationChartsGridComponent,
    SecureCXMonitoringGridComponent,
    OneViewGraphComponent,
    ChipAutocompleteComponent,
    SelectAutocompleteComponent,
    LocationAutocompleteComponent,
    ThresholdAutocompleteComponent,
    ReportTypeAutocompleteComponent,

    // pipes 
    // TODO : Transfer this into SharedModule
    CanAccessPipe,
    ExtractPipe,
    ContainsPipe,
    JsonParsePipe,
    ReplacePipe,
    NetworkTypePipe,
    MOSRatePipe,
    DefaultPipe,
    DateAgoPipe,
    TimezonePipe,
    RemoveCommaPipe
  ],
  imports: [
    DashboardRoutingModule,
    CommonModule,
    MatTooltipModule,
    MatBadgeModule,
    MatPaginatorModule,
    MatChipsModule,
    MatProgressBarModule,
    MatProgressSpinnerModule,
    MdePopoverModule,
    FormsModule,
    ReactiveFormsModule,
    MatButtonModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatDialogModule,
    MatDatepickerModule,
    MatAutocompleteModule,
    MatCardModule,
    MatTabsModule,
    MatMenuModule,
    MatIconModule,
    MatListModule,
    MatSidenavModule,
    MatExpansionModule
  ],
  providers: [
    DashboardService,
    WorkstationService,
    FlagsService,
    FlagsHelper,
    ToastHelper,
    PaginateFactory,
    SocketService,
    ZohoService,
    NotificationService,
    EventLogsService,
    AccountsService,
    WorkdayService,
    HistoricalService,
    TriggerService,
    UserConfigService,
    CacheService
  ],
  bootstrap: [],
  exports : [ SelectAutocompleteComponent, ReportTypeAutocompleteComponent, ThresholdAutocompleteComponent, ChipAutocompleteComponent, LocationAutocompleteComponent, CanAccessPipe, MeanOpinionScoreComponent, AgentProfileComponent, AgentProfileStandaloneComponent, WebMTRComponent, NewWorkstationProfileComponent, MOSDetailsComponent ]
})
export class DashboardModule{ }
