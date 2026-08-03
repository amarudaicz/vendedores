export interface Seller {
    id: number;
    name: string;
    email: string;
    code: number;
    passwordSalt: string;
    passwordHash: string;
    deleted: boolean;
    createdAt: string;  // Podrías usar Date si lo conviertes al obtenerlo
    updatedAt: string;  // Igual aquí
    isAdmin?: boolean|null;
  }