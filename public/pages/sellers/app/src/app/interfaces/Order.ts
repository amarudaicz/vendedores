export type StatusKey = 'pending' | 'finalized' | 'not_realized';

export interface Order {
    id: number;
    payment_method: string;
    note: string;
    status: StatusKey;
    created_at: string;  // Formato: "YYYY-MM-DD HH:mm:ss"
    updated_at: string;  // Formato: "YYYY-MM-DD HH:mm:ss"
    customer_code: number;
    guest_id: number | null;
    guest:any;
    customer:any;
    customer_name: string;
    customer_dni: string;
    total: string; // Si prefieres que sea numérico, puedes usar number en lugar de string
  }
    