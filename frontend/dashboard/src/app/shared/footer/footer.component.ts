import { CoreEnvironment } from '@angular/compiler/src/compiler_facade_interface';
import { Component } from '@angular/core';
import { environment } from '@env/environment';

@Component({
    selector: 'app-footer-cmp',
    templateUrl: 'footer.component.html'
})

export class FooterComponent {
    test: Date = new Date();
    buildVersion = environment.BUILD_VERSION;
}
