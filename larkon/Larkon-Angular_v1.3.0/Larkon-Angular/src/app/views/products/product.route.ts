import { Route } from '@angular/router'
import { ListComponent } from './list/list.component'
import { AddComponent } from './add/add.component'
import { EditComponent } from './edit/edit.component'

export const PRODUCT_ROUTES: Route[] = [
  {
    path: 'list',
    component: ListComponent,
    data: { title: 'Extensions' },
  },
  {
    path: 'add',
    component: AddComponent,
    data: { title: 'Add Extension' },
  },
  {
    path: 'edit/:id',
    component: EditComponent,
    data: { title: 'Edit Extension' },
  },
]
