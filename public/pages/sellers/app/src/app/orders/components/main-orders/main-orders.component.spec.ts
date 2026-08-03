import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MainOrdersComponent } from './main-orders.component';

describe('MainOrdersComponent', () => {
  let component: MainOrdersComponent;
  let fixture: ComponentFixture<MainOrdersComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [MainOrdersComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(MainOrdersComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
