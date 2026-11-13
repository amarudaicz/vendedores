import { Component, inject, signal, ViewChild, ElementRef, AfterViewInit, OnDestroy } from '@angular/core';
import { ProductService } from '../../services/product.service';
import { CommonModule } from '@angular/common';
import { NgxSkeletonLoaderModule } from 'ngx-skeleton-loader';
import { Product } from '../../../interfaces/product.interface';
import { Subject, debounceTime, distinctUntilChanged, switchMap, of, tap } from 'rxjs';
import { takeUntil } from 'rxjs/operators';

@Component({
  selector: 'app-product-list',
  standalone: true,
  imports: [CommonModule, NgxSkeletonLoaderModule],
  templateUrl: './product-list.component.html',
  styleUrl: './product-list.component.scss',
})
export class ProductListComponent implements AfterViewInit, OnDestroy {
  private productService = inject(ProductService);
  private searchSubject = new Subject<string>();
  private destroy$ = new Subject<void>();

  public displayedProducts = signal<Product[]>([]);
  public error = signal<string | null>(null);
  public isLoading = signal<boolean>(false);
  public hasSearched = signal<boolean>(false);

  @ViewChild('scrollContainer') scrollContainer!: ElementRef<HTMLDivElement>;

  constructor() {
    this.searchSubject.pipe(
      debounceTime(400),
      distinctUntilChanged(),
      tap((query) => {
        if (!query) {
          this.displayedProducts.set([]);
          this.hasSearched.set(false);
          this.isLoading.set(false);
        }
      }),
      switchMap((query) => {
        if (!query) return of(null);
        this.isLoading.set(true);
        this.error.set(null);
        return this.productService.searchProducts(query);
      }),
      takeUntil(this.destroy$)
    ).subscribe({
      next: (response) => {
        if (response === null) return;
        this.displayedProducts.set(response.data.products ?? []);
        this.hasSearched.set(true);
        this.isLoading.set(false);
      },
      error: () => {
        this.error.set('No se pudieron cargar los productos. Inténtalo de nuevo más tarde.');
        this.isLoading.set(false);
      }
    });
  }

  ngAfterViewInit(): void {}

  onSearch(event: Event): void {
    const input = event.target as HTMLInputElement;
    this.searchSubject.next(input.value.trim());
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
