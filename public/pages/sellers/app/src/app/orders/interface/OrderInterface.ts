export interface Stat {
    count: number,
    percentage: number
};

export interface OrderStats {
    total_orders: Stat
    pending_orders: Stat;
    finalized_orders: Stat;
    not_realized_orders: Stat;
    total_invoiced: Stat;
}