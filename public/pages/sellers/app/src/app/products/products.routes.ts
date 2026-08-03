import { Routes } from '@angular/router';
import { ProductListComponent } from './components/product-list/product-list.component';

// Estas son las rutas para la sección de "Productos"
export const PRODUCTS_ROUTES: Routes = [
  {
    path: '', // La ruta vacía (que será /products)
    component: ProductListComponent, // Carga el componente de la lista
  },
];