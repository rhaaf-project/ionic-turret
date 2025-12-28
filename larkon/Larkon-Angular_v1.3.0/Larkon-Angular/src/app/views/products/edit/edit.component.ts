import { Component, OnInit, Inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, ActivatedRoute, RouterLink } from '@angular/router';
import { ExtensionService, Extension } from '../../../core/services/extension.service';

@Component({
  selector: 'app-edit',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './edit.component.html'
})
export class EditComponent implements OnInit {
  title = 'EDIT EXTENSION';
  extensionForm: FormGroup;
  isSubmitting = false;
  extensionId: number | null = null;
  isLoading = true;

  constructor(
    private fb: FormBuilder,
    @Inject(ExtensionService) private extensionService: ExtensionService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    this.extensionForm = this.fb.group({
      extension: ['', [Validators.required]],
      name: ['', [Validators.required]],
      secret: ['', [Validators.required]],
      context: ['from-internal', [Validators.required]],
      is_active: [true]
    });
  }

  ngOnInit(): void {
    this.extensionId = Number(this.route.snapshot.paramMap.get('id'));
    if (this.extensionId) {
      this.fetchExtension(this.extensionId);
    }
  }

  fetchExtension(id: number) {
    this.extensionService.getExtension(id).subscribe({
      next: (data: Extension) => {
        this.extensionForm.patchValue(data);
        this.isLoading = false;
      },
      error: (error: any) => {
        console.error('Error fetching extension:', error);
        this.isLoading = false;
        // TODO: Handle error
      }
    });
  }

  onSubmit() {
    if (this.extensionForm.valid && this.extensionId) {
      this.isSubmitting = true;
      this.extensionService.updateExtension(this.extensionId, this.extensionForm.value).subscribe({
        next: () => {
          this.router.navigate(['/extensions/list']);
        },
        error: (error: any) => {
          console.error('Error updating extension:', error);
          this.isSubmitting = false;
        }
      });
    }
  }
}
