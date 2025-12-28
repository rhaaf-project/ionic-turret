import { CommonModule } from '@angular/common';
import { Component, Input } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-logo-box',
  standalone: true,
  imports: [RouterLink, CommonModule],
  template: `
    <div [class]="className">
      <a routerLink="/">
        <img src="assets/images/logo-dark.jpg" class="logo-lg" alt="SmartX" />
      </a>
    </div>
  `,
})
export class LogoBoxComponent {
  @Input() className: string = '';
  @Input() size: boolean = false;
}


