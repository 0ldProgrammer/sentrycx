import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DashboardPage } from './pages';

const routes : Routes = [
    { path : 'client/dashboard', component : DashboardPage }
];

@NgModule({
    imports: [ RouterModule.forRoot( routes ) ],
    exports: [ RouterModule ]
})
export class ClientRoutingModule {}