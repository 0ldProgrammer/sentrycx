import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { NavbarComponent } from './navbar.component';
import { ClockComponent } from './../clock/clock.component';
import { TimezoneComponent } from '../timezone/timezone.component';
import { FormsModule,ReactiveFormsModule } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatSelectModule } from '@angular/material/select';

@NgModule({
    imports: [ RouterModule, CommonModule, FormsModule, ReactiveFormsModule , MatFormFieldModule, MatSelectModule ],
    declarations: [ NavbarComponent, ClockComponent, TimezoneComponent ],
    exports: [ NavbarComponent, ClockComponent, TimezoneComponent ]
})

export class NavbarModule {}
