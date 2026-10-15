export interface Product {
  id: number;
  code: string;
  name: string;
  description: string | null;
  price: number;
  arsUsd:number;
  stock: number;
  unit: string | null;         
  cantidadPorCaja: number | null;    
  created_at: string;
  updated_at: string;
}
