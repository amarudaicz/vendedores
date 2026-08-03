import { HttpClient } from '@angular/common/http';
import {
  AfterViewChecked,
  AfterViewInit,
  Component,
  ElementRef,
  EventEmitter,
  Input,
  OnChanges,
  OnInit,
  Output,
  QueryList,
  SimpleChange,
  SimpleChanges,
  ViewChild,
  ViewChildren,
  input,
} from '@angular/core';
import {
  FormArray,
  FormBuilder,
  FormGroup,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';
import { Subject, catchError, debounceTime, distinctUntilChanged, filter, of, switchMap } from 'rxjs';
import AlertService from '../../../shared/services/alert/alert.service';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { environment } from '../../../../../environment';
import { Product } from '../../../interfaces/product.interface';
import { globalConfig } from '../../../global-config';

@Component({
  selector: 'app-items-list',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './items-list.component.html',
  styleUrl: './items-list.component.scss',
})
export class ItemsListComponent implements AfterViewChecked, OnChanges, OnInit {
  itemsFormArray!: FormArray;
  formNewProduct: FormGroup;

  @Input() form!: FormGroup;

  @Input() customer: any;
  @Output() itemsEmitter: EventEmitter<any> = new EventEmitter();

  private querySubjects: Subject<string>[] = []; // Almacena Subjects individuales para cada input.
  private subscriptions: any[] = []; // Para gestionar las suscripciones.
  globalConfig = globalConfig;
  suggestions: any[] | undefined = undefined;
  activeInputIndex: number = 0;
  paginator: any = {
    page: 1,
  };

  isSetup: boolean = false;
  @ViewChildren('scrollAnchor') scrollAnchors!: QueryList<ElementRef>;
  @ViewChildren('scrollContainer') scrollContainers!: QueryList<ElementRef>;

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private alertService: AlertService,
    private router: Router,
  ) {
    this.formNewProduct = fb.group({
      query: ['', [Validators.required]], // Código del producto
      quantity: [1, [Validators.required, Validators.min(1)]], // Cantidad inicial
      product: [null, [Validators.required]], // Producto seleccionado,
      discount: [0],
    });

    this.formNewProduct
      .get('query')
      ?.valueChanges.pipe(
        debounceTime(300),
        switchMap((query: string) => {
          if (!query) {
            return of(null);
          }
          return this.fetchSuggestions(query).pipe(
            catchError((error) => {
              return of({ data: { products: [], paginator: {} } }); // Valor por defecto
            }),
          );
        }),
      )
      .subscribe((res: any) => {
        if (!res) {
          this.formNewProduct.get('product')?.setValue(null);
          this.suggestions = undefined;
          return;
        }
        this.formNewProduct.get('product')?.setValue(null);
        this.suggestions = res.data.products;
        this.paginator = res.data;
      });
  }

  private observedElements = new Set<string>();
  private observers = new Map<string, IntersectionObserver>();
  private prevCustomer: any;

  ngOnInit(): void {
    this.itemsFormArray = this.form.get('items') as FormArray;

    this.querySubjects = [];

    this.form.valueChanges.subscribe((value: any) => {
      console.log(value);
    });
  }

  ngOnChanges(changes: SimpleChanges): void {
    const customerChange: SimpleChange = changes['customer'];

    if (customerChange && !customerChange.firstChange) {
      this.setCustomer(customerChange.currentValue);
    }
  }

  ngAfterViewChecked(): void {
    this.setupObservers();
    this.itemsEmitter.emit(
      this.itemsFormArray.value.filter((e: any) => e.product),
    );
  }

  setCustomer(customer: any) {
    if (
      this.prevCustomer &&
      customer &&
      customer.price_list !== this.prevCustomer.price_list
    ) {
      this.prevCustomer = customer;
      this.itemsFormArray.clear(); // Clear the items form array
      this.formNewProduct.patchValue(
        {
          product: null,
          query: '',
          discount: 0,
        },
        { emitEvent: false },
      );

      this.suggestions = undefined;
      this.alertService.showAlert(
        'Se ha cambiado el cliente, se han eliminado los productos',
        'info',
      );
    } else if (customer) {
      this.prevCustomer = customer;
    }
  }

  getTotalPedido(): number {
    const items = this.itemsFormArray.value;
    const generalDiscount = this.form.get('discount')?.value || 0; // Obtiene el descuento general o 0 si no está definido

    let total = 0;
    items.forEach((item: any) => {
      const precioConDescuento =
        item.product.price * (1 - (item.discount || 0) / 100);
      total += precioConDescuento * item.quantity;
    });

    // Aplicar descuento general
    total = total * (1 - generalDiscount / 100);

    return total;
  }

  setupObservers() {
    this.scrollAnchors.forEach((anchor, index) => {
      const container = this.scrollContainers.toArray()[index];
      if (anchor && container) {
        const anchorId = anchor.nativeElement.id;
        const containerId = container.nativeElement.id;
        if (!this.observedElements.has(anchorId)) {
          console.log('Configurando observer para:', anchorId, containerId);
          this.setupIntersectionObserver(
            anchor.nativeElement,
            container.nativeElement,
          );
          this.observedElements.add(anchorId);
        }
      }
    });
  }

  setupIntersectionObserver(anchor: HTMLElement, container: HTMLElement) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            console.log('Intersección detectada:', anchor.id);
            this.loadMoreSuggestions();
          }
        });
      },
      {
        root: container, // El contenedor con overflow-scroll
        rootMargin: '0px',
        threshold: 0.1, // Se activa cuando el 10% del elemento está visible
      },
    );

    observer.observe(anchor);
    this.observers.set(anchor.id, observer);
  }

  addItem() {
    const formValue = this.formNewProduct.value;

    if (!formValue.product) {
      this.formNewProduct.markAllAsTouched();
      this.alertService.showAlert(
        'Busca un producto antes de agregar',
        'error',
      );
      return;
    }

    if(formValue.quantity <= 0){
      this.alertService.showAlert(
        'La cantidad debe ser mayor a 0',
        'error',
      );
      return;
    }

    if(formValue.quantity > formValue.product.stock){
      this.alertService.showAlert(
        'No hay stock suficiente',
        'error',
      );
      return;
    }

    this.itemsFormArray.push(
      this.fb.group({
        product: [formValue.product],
        quantity: [formValue.quantity],
        discount: [formValue.discount],
      }),
    );

    this.formNewProduct.patchValue(
      {
        query: '',
        product: null,
        quantity: 1,
      },
      { emitEvent: false },
    );
    this.suggestions = undefined;
  }

  onInputChange(index: number, query: string) {
    if (this.querySubjects[index]) {
      this.querySubjects[index].next(query); // Emitir el nuevo valor al Subject correspondiente.
      this.activeInputIndex = index; // Actualiza el índice activo cuando se escribe
      this.itemsFormArray.at(index).patchValue({ product: null }); // Reinicia el producto seleccionado
    }
  }

  preventEnter(event: any) {
    event.preventDefault();
  }

  fetchSuggestions(query: string, page: number = 1, perPage: number = 10) {
    // Asegurar que customer_price sea válido (entre 1 y 6)
    let customer_price = this.customer?.price_list || 1;
    if (customer_price < 1 || customer_price > 6) {
      customer_price = 1;
    }

    const apiUrl = `${environment.apiUrl}sellers/products?query=${query}&customer_price=${customer_price}&page=${page}&per_page=${perPage}`;
    return this.http.get<any[]>(apiUrl, { withCredentials: true });
  }

  loadMoreSuggestions() {
    if (this.paginator.page === this.paginator.totalPages || this.formNewProduct.get('product')?.value) return;

    const query = this.formNewProduct.get('query')?.value;

    if (query) {
      this.fetchSuggestions(query, this.paginator.page + 1)
        .pipe(
          catchError((error) => {
            console.error('Error en la solicitud:', error);
            return of({ data: { products: [], paginator: {} } }); // Valor por defecto para no romper el flujo
          }),
        )
        .subscribe((res: any) => {
          this.suggestions = [
            ...(this.suggestions || []),
            ...res.data.products,
          ];
          this.paginator.page++;
        });
    }
  }

  removeItem(index: number) {
    this.itemsFormArray.removeAt(index);
  }

  selectSuggestion(suggestion: any) {
    if (!suggestion.stock || suggestion.stock < 0) {
      this.alertService.showAlert('No hay stock del producto', 'error');
      return;
    }

    this.formNewProduct.patchValue(
      { product: suggestion, query: suggestion.name },
      { emitEvent: false },
    );

    this.resetSuggestions();
  }

  resetSuggestions() {
    this.suggestions = undefined;
  }

  onBlurInputProduct() {
    this.resetSuggestions();
  }

  getPriceArs(product: Product) {
    return this.globalConfig.cotizacion ? product.price * product.arsUsd: product.price;
  }
}
