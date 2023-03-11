import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DeviceCheckPage, DisableEmailPage } from './pages';

const routes : Routes = [
    { path : 'client/device-check', component : DeviceCheckPage },
    { path : 'reports/disable', component : DisableEmailPage }
];

@NgModule({
    imports: [ RouterModule.forRoot( routes ) ],
    exports: [ RouterModule ]
})
export class StaticRoutingModule {}