import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { CookieService } from 'ngx-cookie-service';
import { environment } from '@/environments/environment';

export const apiInterceptor: HttpInterceptorFn = (req, next) => {
    const cookieService = inject(CookieService);
    const authToken = cookieService.get('_LARKON_AUTH_SESSION_KEY_');

    // Add API base URL for relative paths
    let apiReq = req;
    if (req.url.startsWith('/api')) {
        apiReq = req.clone({
            url: `${environment.apiUrl}${req.url.substring(4)}` // Remove '/api' prefix
        });
    }

    // Add auth token if available
    if (authToken) {
        apiReq = apiReq.clone({
            setHeaders: {
                Authorization: `Bearer ${authToken}`,
                Accept: 'application/json'
            }
        });
    } else {
        apiReq = apiReq.clone({
            setHeaders: {
                Accept: 'application/json'
            }
        });
    }

    return next(apiReq);
};
