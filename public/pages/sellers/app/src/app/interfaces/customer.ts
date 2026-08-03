export interface Customer{
    code:string,
    dni:string,
    name:string,

    zone:number,
    price_list:number,
    deleted:1|0,
    seller_code:number,
    seller_name?:string
}