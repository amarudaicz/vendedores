export interface Product {
  id: number;
  code: string;
  name: string;
  description: string | null;
  price: number;
  arsUsd:number;
  stock: number;
  unit: string | null;         
  cant_x_caja: number | null;    
  created_at: string;
  updated_at: string;
}
