import { Component, OnInit } from '@angular/core';
import { CommonModule, DecimalPipe, DatePipe } from '@angular/common';
import { NgbDropdownModule, NgbPaginationModule } from '@ng-bootstrap/ng-bootstrap';
import { OrderStatusComponent } from './components/order-status/order-status.component';
import { CallLogService, CallLog } from '../../../core/services/call-log.service';

@Component({
  selector: 'app-orders-list',
  standalone: true,
  imports: [CommonModule, DecimalPipe, DatePipe, NgbDropdownModule, NgbPaginationModule, OrderStatusComponent],
  templateUrl: './orders-list.component.html',
})
export class OrdersListComponent implements OnInit {
  title = 'CALL LOGS';
  logs: CallLog[] = [];
  page = 1;
  pageSize = 30;
  total = 0;

  constructor(private callLogService: CallLogService) { }

  ngOnInit(): void {
    this.fetchLogs();
  }

  fetchLogs(): void {
    this.callLogService.getCallLogs(this.page, this.pageSize).subscribe({
      next: (data: any) => {
        // Handle Laravel pagination response or direct array
        this.logs = data.data ? data.data : data;
        this.total = data.total ? data.total : this.logs.length;
      },
      error: (error: any) => {
        console.error('Error fetching call logs:', error);
      }
    });
  }

  onPageChange(page: number): void {
    this.page = page;
    this.fetchLogs();
  }
}
