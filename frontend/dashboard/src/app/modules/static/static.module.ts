import { NgModule } from '@angular/core';

import { DeviceCheckPage, DisableEmailPage } from './pages';
import { StaticRoutingModule } from  './static.routing';
import { CommonModule } from '@angular/common';

import { MatDividerModule } from '@angular/material/divider';
import { MatListModule } from '@angular/material/list';
import { MatTooltipModule } from '@angular/material/tooltip';
import { WorkstationService, NotificationService } from '@app/services/index';
import {MatCheckboxModule} from '@angular/material/checkbox';


// TODO : Transfer this into client module
@NgModule({
    declarations: [
        DeviceCheckPage,
        DisableEmailPage
    ],
    imports: [ 
        CommonModule,
        MatDividerModule,
        MatListModule,
        StaticRoutingModule,
        MatTooltipModule,
        MatCheckboxModule,
    ],
    exports: [],
    providers: [ WorkstationService,NotificationService ],
})
export class StaticModule {}