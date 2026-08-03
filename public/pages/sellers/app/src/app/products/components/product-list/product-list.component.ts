import { Component, OnInit, inject, signal, ViewChild, ElementRef, AfterViewInit } from '@angular/core';
import { ProductService } from '../../services/product.service';
import { ApiRes } from '../../../interfaces/apiRes'; 
import { CommonModule } from '@angular/common';
import { NgxSkeletonLoaderModule } from 'ngx-skeleton-loader';
import { Product } from '../../../interfaces/product.interface';

@Component({
  selector: 'app-product-list',
  standalone: true,
  imports: [CommonModule, NgxSkeletonLoaderModule],
  templateUrl: './product-list.component.html',
  styleUrl: './product-list.component.scss',
})
export class ProductListComponent implements OnInit, AfterViewInit {
  private productService = inject(ProductService);

  public allProducts: Product[] = [];
  public filteredProducts: Product[] = [];
  public displayedProducts = signal<Product[]>([]);
  public error = signal<string | null>(null);
  public isLoadingMore = signal<boolean>(false);
  public initialLoading = signal<boolean>(true);

  private itemsPerLoad = 20;
  private currentIndex = 0;

  @ViewChild('scrollContainer') scrollContainer!: ElementRef<HTMLDivElement>;

  ngOnInit(): void {
    this.productService.getProducts().subscribe({
      next: (response) => {
        this.allProducts = response.data.products;
        this.filteredProducts = [...this.allProducts];
        this.loadMoreProducts();
        this.initialLoading.set(false);
      },
      error: (err) => {
        this.error.set('No se pudieron cargar los productos. Inténtalo de nuevo más tarde.');
        this.initialLoading.set(false);
      }
    });
  }

  ngAfterViewInit(): void {
    if (this.scrollContainer) {
      this.scrollContainer.nativeElement.addEventListener('scroll', () => this.onScroll());
    }
  }

  loadMoreProducts(): void {
    if (this.isLoadingMore() || this.currentIndex >= this.filteredProducts.length) return;
    
    this.isLoadingMore.set(true);
    
    setTimeout(() => {
      const endIndex = Math.min(this.currentIndex + this.itemsPerLoad, this.filteredProducts.length);
      const newProducts = this.filteredProducts.slice(this.currentIndex, endIndex);
      this.displayedProducts.set([...this.displayedProducts(), ...newProducts]);
      this.currentIndex = endIndex;
      this.isLoadingMore.set(false);
    }, 300);
  }

  onScroll(): void {
    if (!this.scrollContainer || this.isLoadingMore()) return;

    const element = this.scrollContainer.nativeElement;
    const scrollPosition = element.scrollTop + element.clientHeight;
    const scrollHeight = element.scrollHeight;

    if (scrollHeight - scrollPosition < 150 && this.currentIndex < this.filteredProducts.length) {
      this.loadMoreProducts();
    }
  }

  onSearch(event: Event): void {
    const input = event.target as HTMLInputElement;
    const searchTerm = input.value.toLowerCase().trim();

    if (!searchTerm) {
      this.filteredProducts = [...this.allProducts];
    } else {
      this.filteredProducts = this.allProducts.filter(product => 
        product.name.toLowerCase().includes(searchTerm) || 
        product.code.toString().toLowerCase().includes(searchTerm)
      );
    }

    this.currentIndex = 0;
    this.displayedProducts.set([]);
    this.loadMoreProducts();
    
    if (this.scrollContainer) {
      this.scrollContainer.nativeElement.scrollTop = 0;
    }
  }
}
