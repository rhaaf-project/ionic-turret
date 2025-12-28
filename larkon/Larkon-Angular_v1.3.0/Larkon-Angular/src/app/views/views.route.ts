import { Route } from '@angular/router';
import { DashboardComponent } from './dashboard/dashboard.component';

export const VIEW_ROUTES: Route[] = [
  // Dashboard
  {
    path: 'index',
    component: DashboardComponent,
    data: { title: 'Dashboard' },
  },
  // Extensions (using products module)
  {
    path: 'extensions',
    loadChildren: () =>
      import('./products/product.route').then((mod) => mod.PRODUCT_ROUTES),
  },
  // Channels (using inventory module)
  {
    path: 'channels',
    loadChildren: () =>
      import('./inventory/inventory.route').then((mod) => mod.INVENTORY_ROUTES),
  },
  // Call Logs (using orders module)
  {
    path: 'call-logs',
    loadChildren: () =>
      import('./orders/orders.route').then((mod) => mod.ORDER_ROUTES),
  },
  // Users (using customer module)
  {
    path: 'users',
    loadChildren: () =>
      import('./customer/customer.route').then((mod) => mod.CUSTOMERS_ROUTES),
  },
  // Roles
  {
    path: 'roles',
    loadChildren: () =>
      import('./roles/role.route').then((mod) => mod.ROLE_ROUTES),
  },
];

