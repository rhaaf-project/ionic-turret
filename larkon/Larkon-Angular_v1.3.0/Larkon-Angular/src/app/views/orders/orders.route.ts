import { Route } from '@angular/router'
import { OrdersListComponent } from './orders-list/orders-list.component'
import { OrderDetailComponent } from './order-detail/order-detail.component'

export const ORDER_ROUTES: Route[] = [
  {
    path: 'list',
    component: OrdersListComponent,
    data: { title: 'Call Logs' },
  },
  {
    path: 'detail',
    component: OrderDetailComponent,
    data: { title: 'Call Details' },
  },
]
