export interface ApiRes<T> {
  success?: boolean;
  data: T;
  message?: string;
  code?: number;
  errors?: Record<string, string[]>;
}
