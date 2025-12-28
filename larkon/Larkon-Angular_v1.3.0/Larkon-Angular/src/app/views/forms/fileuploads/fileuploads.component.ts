import { Component } from '@angular/core';
import { FileUploaderComponent } from '@component/file-uploader/file-uploader.component';

@Component({
  selector: 'app-fileuploads',
  standalone: true,
  imports: [FileUploaderComponent],
  templateUrl: './fileuploads.component.html',
  styles: ``
})
export class FileuploadsComponent {
title="File Uploads"
}
