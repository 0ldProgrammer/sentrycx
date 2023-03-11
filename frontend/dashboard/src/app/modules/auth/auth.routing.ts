import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import {
  LoginPage,
  SSOLoginPage,
  LogoutPage,
  ExpiredPage,
  MediaDevicesPage,
  IndexPage
} from './pages';
import { AuthLayoutComponent } from '@layouts/auth/auth-layout.component';

const authRoutes: Routes = [{
    path : '',
    component : IndexPage
  },{ 
    path: 'auth', 
    component: AuthLayoutComponent ,
    children : [
      { path: 'login', component: LoginPage},
      { path: 'validate', component: SSOLoginPage},
      { path: 'session-expired', component : ExpiredPage },
      { path : 'media-devices', component : MediaDevicesPage }
    ]
  },
];
@NgModule({
  imports: [RouterModule.forRoot(authRoutes)],
  exports: [RouterModule]
})
export class AuthRoutingModule { }
