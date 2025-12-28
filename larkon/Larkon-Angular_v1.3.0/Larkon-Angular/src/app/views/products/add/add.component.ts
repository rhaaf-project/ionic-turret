import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { ExtensionService } from '../../../core/services/extension.service';

@Component({
  selector: 'app-add',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './add.component.html'
})
export class AddComponent {
  title = 'ADD EXTENSION';
  extensionForm: FormGroup;
  isSubmitting = false;

  constructor(
    private fb: FormBuilder,
    private extensionService: ExtensionService,
    private router: Router
  ) {
    this.extensionForm = this.fb.group({
      extension: ['', [Validators.required]],
      name: ['', [Validators.required]],
      secret: ['', [Validators.required]],
      context: ['from-internal', [Validators.required]],
      is_active: [true]
    });
  }

  onSubmit() {
    if (this.extensionForm.valid) {
      this.isSubmitting = true;
      this.extensionService.addExtension(this.extensionForm.value).subscribe({
        next: () => {
          this.router.navigate(['/extensions/list']);
        },
        error: (error: any) => {
          console.error('Error creating extension:', error);
          this.isSubmitting = false;
          // TODO: Show toast error
        }
      });
    }
  }
}
