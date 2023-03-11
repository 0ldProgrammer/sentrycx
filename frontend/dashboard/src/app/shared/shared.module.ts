import { NgModule } from '@angular/core';
import { UserConfigService } from '../core/services';
import { DatePipe } from '@angular/common';

// import { NameComponent } from './name.component';
// import { SidebarComponent } from './index';

@NgModule({
    imports: [],
    // exports: [ SidebarComponent ],
    // declarations: [SidebarComponent],
    exports: [  ],
    declarations: [],
    providers: [ UserConfigService, DatePipe ],
})
export class SharedModule { }
