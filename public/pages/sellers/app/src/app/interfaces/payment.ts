export interface Receipt {
    id: number;
    customer_code: number;
    seller_code: number;
    type_receipt: string;
    number_receipt: number;
    date_receipt: string; // Podrías usar Date si lo transformas al obtenerlo
    subtotal_receipt: string; // Si es un número, considera cambiarlo a number
    total_receipt: string;
    balance_receipt: string;
    iva_receipt: string;
    condition_sale: number;
    delay_receipt: number;
    balance_accumulated: string;
}
