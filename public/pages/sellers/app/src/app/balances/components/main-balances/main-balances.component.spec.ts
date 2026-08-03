import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MainBalancesComponent } from './main-balances.component';

describe('MainBalancesComponent', () => {
  let component: MainBalancesComponent;
  let fixture: ComponentFixture<MainBalancesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [MainBalancesComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(MainBalancesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
