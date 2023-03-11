import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { CommonModule } from '@angular/common';
import { BrowserModule } from '@angular/platform-browser'
import {
    LoginPage,
    SSOLoginPage,
    MediaDevicesPage,
    IndexPage
} from './pages';
import { AuthService } from '../../core/services/auth.services';
import { AuthRoutingModule } from './auth.routing';
import {MatProgressBarModule} from '@angular/material/progress-bar';

@NgModule({
    declarations: [
        LoginPage,
        SSOLoginPage,
        MediaDevicesPage,
        IndexPage
    ],
    imports: [
        CommonModule,
        BrowserModule,
        AuthRoutingModule,
        MatProgressBarModule
    ],
    exports: [
        RouterModule
    ],
    providers: [AuthService]
})
export class AuthModule { }
