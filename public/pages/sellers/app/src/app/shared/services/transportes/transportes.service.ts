import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { Transporte } from '../../../interfaces/transporte.interface';
import { environment } from '../../../../../environment';

@Injectable({
  providedIn: 'root'
})
export class TransportesService {

  private transportesSubject = new BehaviorSubject<Transporte[]>([]);
  transportes$ = this.transportesSubject.asObservable();

  private loaded = false;

  constructor(private http: HttpClient) {}

  /**
   * Carga transportes desde la API (solo una vez, cacheado en BehaviorSubject).
   */
  getTransportes(): Observable<Transporte[]> {
    if (!this.loaded) {
      this.http.get<{ data: Transporte[] | { transportes: Transporte[] } }>(
        `${environment.apiUrl}transportes`,
        { withCredentials: true }
      ).pipe(
        tap(res => {
          const list = Array.isArray(res.data)
            ? res.data
            : (res.data && 'transportes' in res.data ? res.data.transportes : []);
          this.transportesSubject.next(list ?? []);
          this.loaded = true;
        })
      ).subscribe();
    }
    return this.transportes$;
  }
}

