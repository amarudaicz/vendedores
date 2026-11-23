import { Transporte } from './transporte.interface';

export type StatusKey =
  | 'pending'
  | 'confirmed'
  | 'in_progress'
  | 'finalized'
  | 'not_realized';

/**
 * Transiciones de estado válidas (forward-only).
 * Espejo exacto de Order::STATUS_FLOW en el backend.
 */
export const STATUS_FLOW: Record<StatusKey, StatusKey[]> = {
  pending: ['confirmed'],
  confirmed: ['in_progress'],
  in_progress: ['finalized'],
  finalized: [],
  not_realized: [],
};

/**
 * Etiquetas legibles por estado.
 */
export const STATUS_LABELS: Record<StatusKey, string> = {
  pending: 'Pendiente',
  confirmed: 'Confirmado',
  in_progress: 'En proceso',
  finalized: 'Despachado',
  not_realized: 'No Concretada',
};

export interface OrderItem {
  id?: number;
  orderId?: number;
  productCode?: string;
  product_code?: string;
  description: string;
  price: number;
  quantity: number;
  subtotal?: number;
}

export interface Order {
  id: number;
  status: StatusKey;
  statusLabel?: string;
  paymentMethod: string | null;
  deliveryMethod: string | null;
  note: string | null;
  customerCode: string | number | null;
  customerZone: string | number | null;
  customerName: string | null;
  customerDni: string | null;
  guestId: number | null;
  sellerCode: string | number | null;
  sellerName: string | null;
  idTransporte: number | null;
  transporteName: string | null;
  cotizacion: number;
  discount: number;
  total: number | string;
  itemsCount?: number;
  createdAt: string;
  updatedAt: string;
  customer?: any;
  guest?: any;
  transporte?: Transporte | null;
  items?: OrderItem[];
}

export interface OrderDetail extends Order {
  deliveryProvince?: string | null;
  deliveryLocality?: string | null;
  deliveryPostalCode?: string | null;
  deliveryAddress?: string | null;
  subtotal?: number;
  discountPercent?: number;
  discountAmount?: number;
  total: number | string;
  customer?: any;
  guest?: any;
  transporte?: Transporte | null;
  items: OrderItem[];
}
