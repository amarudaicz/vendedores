import { ComponentFixture, TestBed } from '@angular/core/testing';

import { StatsOrdersComponent } from './stats-orders.component';

describe('StatsOrdersComponent', () => {
  let component: StatsOrdersComponent;
  let fixture: ComponentFixture<StatsOrdersComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [StatsOrdersComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(StatsOrdersComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
