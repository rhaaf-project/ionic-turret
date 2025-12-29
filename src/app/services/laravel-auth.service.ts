import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, of, throwError } from 'rxjs';
import { map, catchError, switchMap } from 'rxjs/operators';

export interface LoginResponse {
    token: string;
    user: {
        id: number;
        name: string;
        email: string;
    };
}

export interface Extension {
    id: number;
    extension: string;
    name: string;
    secret: string;
    context?: string;
    is_active?: boolean;
    sip_server?: string;
    created_at?: string;
    updated_at?: string;
}

export interface AuthResult {
    success: boolean;
    token?: string;
    extension?: Extension;
    error?: string;
}

@Injectable({ providedIn: 'root' })
export class LaravelAuthService {
    private readonly API_URL = 'http://103.154.80.171/api';
    private token: string | null = null;

    constructor(private http: HttpClient) {
        // Load token from storage if exists
        this.token = localStorage.getItem('smartucx_auth_token');
    }

    /**
     * Login and get user's assigned extension
     * @param username - username or email
     * @param password - user password
     * @returns AuthResult with token and extension data
     */
    login(username: string, password: string): Observable<AuthResult> {
        const headers = new HttpHeaders({
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        });

        return this.http.post<LoginResponse>(`${this.API_URL}/login`, {
            email: username.includes('@') ? username : `${username}@smartx.local`,
            password: password
        }, { headers }).pipe(
            switchMap(response => {
                // Store token
                this.token = response.token;
                localStorage.setItem('smartucx_auth_token', response.token);
                console.log('[Auth] Login successful, token received');

                // Get extensions and find matching one
                return this.getExtensions().pipe(
                    map(extensions => {
                        // Find extension matching user (e.g. admin gets 6000, demo1 gets 6001)
                        // For now, match by name or return first extension
                        let userExtension = extensions.find(e =>
                            e.name?.toLowerCase() === username.toLowerCase() ||
                            e.extension === username
                        );

                        // Fallback: return first active extension
                        if (!userExtension) {
                            userExtension = extensions.find(e => e.is_active !== false) || extensions[0];
                        }

                        return {
                            success: true,
                            token: this.token || undefined,
                            extension: userExtension
                        } as AuthResult;
                    })
                );
            }),
            catchError(err => {
                console.error('[Auth] Login failed:', err);
                return of({
                    success: false,
                    error: err.error?.message || err.message || 'Login failed'
                } as AuthResult);
            })
        );
    }

    /**
     * Get all extensions from Laravel API
     */
    getExtensions(): Observable<Extension[]> {
        const headers = new HttpHeaders({
            'Accept': 'application/json',
            'Authorization': `Bearer ${this.token}`
        });

        return this.http.get<Extension[]>(`${this.API_URL}/extensions`, { headers }).pipe(
            catchError(err => {
                console.error('[Auth] Failed to fetch extensions:', err);
                return of([]);
            })
        );
    }

    /**
     * Get specific extension by ID
     */
    getExtension(id: number): Observable<Extension | null> {
        const headers = new HttpHeaders({
            'Accept': 'application/json',
            'Authorization': `Bearer ${this.token}`
        });

        return this.http.get<Extension>(`${this.API_URL}/extensions/${id}`, { headers }).pipe(
            catchError(err => {
                console.error('[Auth] Failed to fetch extension:', err);
                return of(null);
            })
        );
    }

    /**
     * Logout and clear token
     */
    logout(): void {
        this.token = null;
        localStorage.removeItem('smartucx_auth_token');
        console.log('[Auth] Logged out');
    }

    /**
     * Check if user is authenticated
     */
    isAuthenticated(): boolean {
        return !!this.token;
    }

    /**
     * Get current token
     */
    getToken(): string | null {
        return this.token;
    }
}
