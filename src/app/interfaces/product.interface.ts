export interface Product {
  id?: number;
  code: string;
  name: string;
  description: string | null;
  price: number;
  basePrice?: number;
  arsUsd: number;
  stock: number;
  unit?: string | null;
  categoryCode?: string | null;
  categoryName?: string | null;
  subcategoryCode?: string | null;
  subcategoryName?: string | null;
  featured?: boolean;
  rating?: number | null;
  cantidadPorCaja?: number | null;
  updatedAt?: string | null;
  imagePath?: string | null;
}

