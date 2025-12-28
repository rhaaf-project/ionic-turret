import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface Extension {
    id: number;
    extension: string;
    name: string;
    secret: string;
    context: string;
    is_active: boolean;
    sip_server?: string;
    created_at?: string;
    updated_at?: string;
}

@Injectable({
    providedIn: 'root'
})
export class ExtensionService {

    constructor(private http: HttpClient) { }

    getExtensions(): Observable<Extension[]> {
        return this.http.get<Extension[]>(`${environment.apiUrl}/extensions`);
    }

    getExtension(id: number): Observable<Extension> {
        return this.http.get<Extension>(`${environment.apiUrl}/extensions/${id}`);
    }

    addExtension(extension: Partial<Extension>): Observable<Extension> {
        return this.http.post<Extension>(`${environment.apiUrl}/extensions`, extension);
    }

    updateExtension(id: number, extension: Partial<Extension>): Observable<Extension> {
        return this.http.put<Extension>(`${environment.apiUrl}/extensions/${id}`, extension);
    }

    deleteExtension(id: number): Observable<void> {
        return this.http.delete<void>(`${environment.apiUrl}/extensions/${id}`);
    }
}
