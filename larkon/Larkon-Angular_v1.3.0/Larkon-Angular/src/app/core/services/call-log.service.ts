import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface CallLog {
    id: number;
    calldate: string;
    clid: string;
    src: string;
    dst: string;
    dcontext: string;
    channel: string;
    dstchannel: string;
    lastapp: string;
    lastdata: string;
    duration: number;
    billsec: number;
    disposition: string;
    amaflags: number;
    accountcode: string;
    uniqueid: string;
    userfield: string;
    recordingfile: string;
}

@Injectable({
    providedIn: 'root'
})
export class CallLogService {

    constructor(private http: HttpClient) { }

    getCallLogs(page: number = 1, perPage: number = 30): Observable<any> {
        return this.http.get<any>(`${environment.apiUrl}/call-logs?page=${page}&per_page=${perPage}`);
    }

    getCallLog(id: number): Observable<CallLog> {
        return this.http.get<CallLog>(`${environment.apiUrl}/call-logs/${id}`);
    }
}
