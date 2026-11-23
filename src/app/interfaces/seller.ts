export interface Seller {
  id: number;
  name: string;
  email: string;
  code: number;
  deleted: boolean;
  createdAt: string;
  updatedAt?: string | null;
  dolar?: number | null;
  isAdmin?: boolean | null;
}
