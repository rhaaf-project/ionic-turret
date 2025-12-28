import { Component, OnInit } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { NgbDropdownModule, NgbPaginationModule } from '@ng-bootstrap/ng-bootstrap';
import { RouterLink } from '@angular/router';
import { ExtensionService, Extension } from '../../../core/services/extension.service';

@Component({
  selector: 'app-list',
  standalone: true,
  imports: [DecimalPipe, NgbPaginationModule, NgbDropdownModule, RouterLink],
  templateUrl: './list.component.html',
})
export class ListComponent implements OnInit {
  title = 'EXTENSIONS';
  extensions: Extension[] = [];

  constructor(private extensionService: ExtensionService) { }

  ngOnInit(): void {
    this.fetchExtensions();
  }

  fetchExtensions(): void {
    this.extensionService.getExtensions().subscribe({
      next: (data: Extension[]) => {
        this.extensions = data;
      },
      error: (error: any) => {
        console.error('Error fetching extensions:', error);
      }
    });
  }

  deleteExtension(id: number): void {
    if (confirm('Are you sure you want to delete this extension?')) {
      this.extensionService.deleteExtension(id).subscribe({
        next: () => {
          this.fetchExtensions();
        },
        error: (error: any) => {
          console.error('Error deleting extension:', error);
        }
      });
    }
  }
}
