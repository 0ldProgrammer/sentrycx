import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ClientRoutingModule } from './client.routing';
import { DashboardPage } from './pages';
import { DashboardModule } from '@modules/index';
import { MatProgressBarModule } from '@angular/material/progress-bar';


@NgModule({
    declarations: [ DashboardPage ],
    imports: [ 
        CommonModule, 
        ClientRoutingModule, 
        DashboardModule,
        MatProgressBarModule
    ],
    exports: [],
    providers: [],
})
export class ClientModule {}