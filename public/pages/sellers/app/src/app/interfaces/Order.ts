// Espejo del STATUS_FLOW definido en el backend (models/Order.php)
export type StatusKey = 'pending' | 'confirmed' | 'in_progress' | 'finalized' ;
// | 'not_realized'

/**
 * Transiciones de estado válidas (forward-only).
 * Espejo exacto de Order::STATUS_FLOW en el backend PHP.
 *
 * Uso: STATUS_FLOW['pending'] → ['confirmed', 'not_realized']
 */
export const STATUS_FLOW: Record<StatusKey, StatusKey[]> = {
  pending:      ['confirmed'],
  confirmed:    ['in_progress'],
  in_progress:  ['finalized'],
  finalized:    [],
  // not_realized: [],
};

/**
 * Etiquetas legibles por estado.
 * Espejo exacto de Order::STATUS_LABELS en el backend PHP.
 */
export const STATUS_LABELS: Record<StatusKey, string> = {
  pending:      'Pendiente',
  confirmed:    'Confirmado',
  in_progress:  'En proceso',
  finalized:    'Despachado',
  // not_realized: 'No Concretada',
};

export interface Order {
  id: number;
  payment_method: string;
  note: string;
  status: StatusKey;
  created_at: string;   // Formato: "YYYY-MM-DD HH:mm:ss"
  updated_at: string;   // Formato: "YYYY-MM-DD HH:mm:ss"
  customer_code: number;
  guest_id: number | null;
  guest: any;
  customer: any;
  customer_name: string;
  customer_dni: string;
  seller_name: string;
  total: string;
}