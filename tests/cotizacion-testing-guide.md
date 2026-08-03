# Cotización (Dollar Exchange Rate) Testing Guide

## Overview

This guide provides comprehensive manual test cases for the Cotización feature, including API endpoints, admin UI, and backwards compatibility verification.

**Feature**: Dollar Exchange Rate Management
**Test Type**: Manual Testing
**Last Updated**: 2026-03-25

---

## Prerequisites

### Database Setup

1. Ensure `cotizacion` table exists in database:
   ```sql
   SELECT * FROM cotizacion WHERE id=1;
   ```

2. Expected table structure:
   - `id` (INT, PRIMARY KEY) - Always 1
   - `valor` (DECIMAL(10,2), NOT NULL) - Exchange rate
   - `created_at` (DATETIME)
   - `updated_at` (DATETIME)

3. Verify at least one record exists:
   ```sql
   SELECT COUNT(*) as count FROM cotizacion;
   -- Expected: 1
   ```

### Server Setup

1. Start PHP development server:
   ```bash
   cd C:\Users\PC AMD\Desktop\tyme-rosario
   php -S localhost:8000 -t .
   ```

2. Verify server is running:
   ```bash
   curl http://localhost:8000/api/v1/cotizacion/dolar
   ```

### Test Accounts

Ensure you have test accounts for different roles:
- **Admin account**: Has account_type = 'admin'
- **Seller account**: Has account_type = 'seller'
- **Customer account**: Has account_type = 'customer'

Create test accounts if needed via existing registration flows or directly in database.

---

## Phase 4.1: GET /api/v1/cotizacion/dolar Testing

### Test Case 4.1.1: Unauthenticated Access (401)

**Test ID**: TC-4.1.1
**Priority**: Critical
**Test Type**: Security

**Steps**:
1. Open terminal or Postman
2. Execute curl command without authentication:
   ```bash
   curl -X GET http://localhost:8000/api/v1/cotizacion/dolar
   ```

**Expected Results**:
- HTTP Status Code: `401 Unauthorized`
- Response contains error message indicating authentication required
- Response format:
  ```json
  {
    "error": "Authentication required"
  }
  ```

**Actual Results**:
- Status: __________
- Response: __________

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.1.2: Authenticated Access - Success (200)

**Test ID**: TC-4.1.2
**Priority**: Critical
**Test Type**: Functional

**Preconditions**:
- User must be logged in (has valid session)

**Steps**:
1. Log in to the application via web interface at `http://localhost:8000/login`
2. Use browser DevTools (Network tab) or use curl with session cookie
3. Execute request with session:
   ```bash
   # First, login to get session cookie
   curl -c cookies.txt -X POST http://localhost:8000/api/v1/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email":"admin@example.com","password":"your_password"}'

   # Then get cotizacion with session
   curl -b cookies.txt -X GET http://localhost:8000/api/v1/cotizacion/dolar
   ```

**Expected Results**:
- HTTP Status Code: `200 OK`
- Response contains current exchange rate
- Response format:
  ```json
  {
    "valor": 1000.50
  }
  ```
- `valor` is a positive decimal with 2 precision

**Actual Results**:
- Status: __________
- Response: __________

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.1.3: Response Format Validation

**Test ID**: TC-4.1.3
**Priority**: High
**Test Type**: Data Integrity

**Steps**:
1. Execute authenticated GET request
2. Verify JSON response structure
3. Validate valor data type

**Expected Results**:
- Response is valid JSON
- Contains exactly one key: `valor`
- `valor` is numeric (decimal)
- `valor` has exactly 2 decimal places
- `valor` is greater than 0

**Validation Checklist**:
- [ ] Valid JSON format
- [ ] Key "valor" exists
- [ ] valor is numeric
- [ ] valor > 0
- [ ] valor has 2 decimal places

**Pass/Fail**: [ ] Pass [ ] Fail

---

## Phase 4.2: PUT /api/v1/cotizacion/dolar Testing

### Test Case 4.2.1: Unauthenticated Update (401)

**Test ID**: TC-4.2.1
**Priority**: Critical
**Test Type**: Security

**Steps**:
1. Execute PUT request without authentication:
   ```bash
   curl -X PUT http://localhost:8000/api/v1/cotizacion/dolar \
     -H "Content-Type: application/json" \
     -d '{"valor": 1050.00}'
   ```

**Expected Results**:
- HTTP Status Code: `401 Unauthorized`
- Error message indicates authentication required
- Database record NOT updated

**Actual Results**:
- Status: __________
- Response: __________
- Database valor unchanged: [ ] Yes [ ] No

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.2.2: Non-Admin Update (403)

**Test ID**: TC-4.2.2
**Priority**: Critical
**Test Type**: Authorization

**Preconditions**:
- Logged in as non-admin (seller or customer)

**Steps**:
1. Log in as seller or customer account
2. Execute PUT request with session:
   ```bash
   # Login as seller
   curl -c cookies.txt -X POST http://localhost:8000/api/v1/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email":"seller@example.com","password":"your_password"}'

   # Attempt update
   curl -b cookies.txt -X PUT http://localhost:8000/api/v1/cotizacion/dolar \
     -H "Content-Type: application/json" \
     -d '{"valor": 1050.00}'
   ```

**Expected Results**:
- HTTP Status Code: `403 Forbidden`
- Error message indicates insufficient permissions
- Database record NOT updated

**Actual Results**:
- Status: __________
- Response: __________
- Database valor unchanged: [ ] Yes [ ] No

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.2.3: Invalid Rate - Negative Value (400)

**Test ID**: TC-4.2.3
**Priority**: Critical
**Test Type**: Validation

**Preconditions**:
- Logged in as admin

**Steps**:
1. Login as admin
2. Send negative valor:
   ```bash
   curl -b cookies.txt -X PUT http://localhost:8000/api/v1/cotizacion/dolar \
     -H "Content-Type: application/json" \
     -d '{"valor": -100.00}'
   ```

**Expected Results**:
- HTTP Status Code: `400 Bad Request`
- Error message indicates validation failure (valor must be positive)
- Database record NOT updated

**Actual Results**:
- Status: __________
- Response: __________
- Database valor unchanged: [ ] Yes [ ] No

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.2.4: Invalid Rate - Zero Value (400)

**Test ID**: TC-4.2.4
**Priority**: Critical
**Test Type**: Validation

**Preconditions**:
- Logged in as admin

**Steps**:
1. Send valor = 0:
   ```bash
   curl -b cookies.txt -X PUT http://localhost:8000/api/v1/cotizacion/dolar \
     -H "Content-Type: application/json" \
     -d '{"valor": 0}'
   ```

**Expected Results**:
- HTTP Status Code: `400 Bad Request`
- Error message indicates validation failure
- Database record NOT updated

**Actual Results**:
- Status: __________
- Response: __________
- Database valor unchanged: [ ] Yes [ ] No

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.2.5: Invalid Rate - Missing Field (400)

**Test ID**: TC-4.2.5
**Priority**: High
**Test Type**: Validation

**Preconditions**:
- Logged in as admin

**Steps**:
1. Send request without valor field:
   ```bash
   curl -b cookies.txt -X PUT http://localhost:8000/api/v1/cotizacion/dolar \
     -H "Content-Type: application/json" \
     -d '{}'
   ```

**Expected Results**:
- HTTP Status Code: `400 Bad Request`
- Error message indicates valor is required
- Database record NOT updated

**Actual Results**:
- Status: __________
- Response: __________
- Database valor unchanged: [ ] Yes [ ] No

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.2.6: Invalid Rate - Non-Numeric Value (400)

**Test ID**: TC-4.2.6
**Priority**: High
**Test Type**: Validation

**Preconditions**:
- Logged in as admin

**Steps**:
1. Send string instead of number:
   ```bash
   curl -b cookies.txt -X PUT http://localhost:8000/api/v1/cotizacion/dolar \
     -H "Content-Type: application/json" \
     -d '{"valor": "invalid"}'
   ```

**Expected Results**:
- HTTP Status Code: `400 Bad Request`
- Error message indicates invalid format
- Database record NOT updated

**Actual Results**:
- Status: __________
- Response: __________
- Database valor unchanged: [ ] Yes [ ] No

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.2.7: Valid Update - Success (200)

**Test ID**: TC-4.2.7
**Priority**: Critical
**Test Type**: Functional

**Preconditions**:
- Logged in as admin
- Current valor exists in database

**Steps**:
1. Record current valor:
   ```bash
   curl -b cookies.txt http://localhost:8000/api/v1/cotizacion/dolar
   # Note the current valor value
   ```

2. Send valid update:
   ```bash
   curl -b cookies.txt -X PUT http://localhost:8000/api/v1/cotizacion/dolar \
     -H "Content-Type: application/json" \
     -d '{"valor": 1050.00}'
   ```

3. Verify database updated:
   ```sql
   SELECT valor, updated_at FROM cotizacion WHERE id=1;
   ```

4. Verify via API:
   ```bash
   curl -b cookies.txt http://localhost:8000/api/v1/cotizacion/dolar
   ```

**Expected Results**:
- HTTP Status Code: `200 OK`
- Response contains success message:
  ```json
  {
    "success": true
  }
  ```
- Database `valor` updated to new value (1050.00)
- Database `updated_at` timestamp refreshed
- API GET returns new valor

**Actual Results**:
- Status: __________
- Response: __________
- Database valor: __________
- updated_at refreshed: [ ] Yes [ ] No
- API GET returns new valor: [ ] Yes [ ] No

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.2.8: Precision Validation (2 decimal places)

**Test ID**: TC-4.2.8
**Priority**: High
**Test Type**: Data Integrity

**Preconditions**:
- Logged in as admin

**Steps**:
1. Send valor with more than 2 decimals:
   ```bash
   curl -b cookies.txt -X PUT http://localhost:8000/api/v1/cotizacion/dolar \
     -H "Content-Type: application/json" \
     -d '{"valor": 1050.123}'
   ```

2. Verify stored value:
   ```sql
   SELECT valor FROM cotizacion WHERE id=1;
   ```

**Expected Results**:
- HTTP Status Code: `200 OK` (or 400 if strict validation)
- Database stores only 2 decimal places (1050.12 or 1050.13 depending on rounding)
- GET endpoint returns valor with 2 decimal places

**Actual Results**:
- Status: __________
- Stored valor: __________
- GET response: __________

**Pass/Fail**: [ ] Pass [ ] Fail

---

## Phase 4.3: Admin UI Testing

### Test Case 4.3.1: Module Loads and Displays Current Rate

**Test ID**: TC-4.3.1
**Priority**: Critical
**Test Type**: Functional/UI

**Preconditions**:
- Admin is logged in
- Current valor exists in database

**Steps**:
1. Open browser to `http://localhost:8000/app`
2. Click "Cotización" in navigation menu
3. Verify module loads

**Expected Results**:
- Module loads without errors
- Current exchange rate is displayed prominently
- Rate matches database value
- Input field is pre-filled with current rate
- UI is responsive (works on mobile)
- No console errors in browser DevTools

**Validation Checklist**:
- [ ] Module loads successfully
- [ ] Current rate displayed
- [ ] Rate matches database
- [ ] Input field pre-filled
- [ ] No console errors
- [ ] Responsive layout

**Actual Results**:
- Module loaded: [ ] Yes [ ] No
- Displayed rate: __________
- Database rate: __________
- Match: [ ] Yes [ ] No
- Console errors: __________

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.3.2: Save Updates Immediately

**Test ID**: TC-4.3.2
**Priority**: Critical
**Test Type**: Functional/UI

**Preconditions**:
- Admin is logged in
- Cotización module is loaded

**Steps**:
1. Note current displayed rate
2. Enter new rate in input field (e.g., 1100.00)
3. Click "Guardar" or "Save" button
4. Observe button loading state
5. Wait for request to complete
6. Verify displayed rate updates immediately
7. Verify database updated:
   ```sql
   SELECT valor, updated_at FROM cotizacion WHERE id=1;
   ```

**Expected Results**:
- Save button shows loading state during request
- Success toast notification appears
- Displayed rate updates immediately to new value
- Database `valor` updated to new value
- Database `updated_at` timestamp refreshed
- No page reload required

**Validation Checklist**:
- [ ] Loading state shown
- [ ] Success toast displayed
- [ ] Rate updates immediately
- [ ] Database updated
- [ ] No page reload

**Actual Results**:
- Loading state: [ ] Yes [ ] No
- Success toast: [ ] Yes [ ] No
- Displayed rate after save: __________
- Database valor: __________
- updated_at refreshed: [ ] Yes [ ] No

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.3.3: Error Toast on Invalid Input

**Test ID**: TC-4.3.3
**Priority**: High
**Test Type**: Error Handling/UI

**Preconditions**:
- Admin is logged in
- Cotización module is loaded

**Steps**:
1. Enter invalid rate (e.g., -100 or 0 or text)
2. Click "Guardar" or "Save" button
3. Observe error message

**Expected Results**:
- Error toast notification appears
- Error message is clear and actionable
- Input field highlights error state
- Displayed rate does NOT change
- Database NOT updated

**Test invalid inputs**:
- [ ] Negative number: -100.00
- [ ] Zero: 0
- [ ] Empty field
- [ ] Text: "invalid"
- [ ] Special characters: @#$%

**Actual Results**:
- Error toast displayed: [ ] Yes [ ] No
- Error message: __________
- Rate unchanged: [ ] Yes [ ] No
- Database unchanged: [ ] Yes [ ] No

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.3.4: Error Toast on Network Failure

**Test ID**: TC-4.3.4
**Priority**: Medium
**Test Type**: Error Handling/UI

**Preconditions**:
- Admin is logged in
- Cotización module is loaded

**Steps**:
1. Disconnect from network or stop server
2. Enter new rate and click save
3. Observe error message

**Expected Results**:
- Error toast notification appears
- Error message indicates network/server issue
- Input field maintains value
- User can retry after connection restored

**Actual Results**:
- Error toast: [ ] Yes [ ] No
- Error message: __________
- Can retry: [ ] Yes [ ] No

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.3.5: UI Accessibility and Responsiveness

**Test ID**: TC-4.3.5
**Priority**: Medium
**Test Type**: UX/Accessibility

**Preconditions**:
- Admin is logged in

**Steps**:
1. Test on different viewport sizes:
   - Desktop (1920x1080)
   - Tablet (768x1024)
   - Mobile (375x667)

2. Test keyboard navigation:
   - Tab to input field
   - Enter value
   - Tab to save button
   - Press Enter to submit

3. Test screen reader compatibility (if available)

**Expected Results**:
- Layout adapts to all screen sizes
- All elements accessible via keyboard
- Input has proper label/ARIA attributes
- Button has proper focus states
- Success/error toasts are announced by screen readers

**Validation Checklist**:
- [ ] Desktop layout works
- [ ] Tablet layout works
- [ ] Mobile layout works
- [ ] Keyboard navigation works
- [ ] Focus states visible
- [ ] Labels/ARIA attributes present

**Actual Results**:
- Desktop: [ ] Pass [ ] Fail
- Tablet: [ ] Pass [ ] Fail
- Mobile: [ ] Pass [ ] Fail
- Keyboard: [ ] Pass [ ] Fail

**Pass/Fail**: [ ] Pass [ ] Fail

---

## Phase 4.4: Backwards Compatibility Testing

### Test Case 4.4.1: Legacy Endpoint Returns Current Rate

**Test ID**: TC-4.4.1
**Priority**: Critical
**Test Type**: Backwards Compatibility

**Preconditions**:
- User is authenticated
- Current valor exists in database

**Steps**:
1. Execute GET on legacy endpoint:
   ```bash
   curl -b cookies.txt http://localhost:8000/api/v1/products/dolar
   ```

2. Compare with new endpoint:
   ```bash
   curl -b cookies.txt http://localhost:8000/api/v1/cotizacion/dolar
   ```

**Expected Results**:
- Both endpoints return identical `valor`
- Both endpoints return HTTP 200
- Response format is same:
  ```json
  {
    "valor": 1050.00
  }
  ```

**Actual Results**:
- Legacy endpoint status: __________
- Legacy endpoint response: __________
- New endpoint status: __________
- New endpoint response: __________
- Values match: [ ] Yes [ ] No

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.4.2: Legacy Endpoint Authentication

**Test ID**: TC-4.4.2
**Priority**: High
**Test Type**: Security/Backwards Compatibility

**Steps**:
1. Execute GET on legacy endpoint without authentication:
   ```bash
   curl http://localhost:8000/api/v1/products/dolar
   ```

**Expected Results**:
- HTTP Status Code: `401 Unauthorized` (same as new endpoint)
- Consistent error handling with new endpoint

**Actual Results**:
- Status: __________
- Error message: __________
- Consistent with new endpoint: [ ] Yes [ ] No

**Pass/Fail**: [ ] Pass [ ] Fail

---

### Test Case 4.4.3: Deprecation Warning Logged

**Test ID**: TC-4.4.3
**Priority**: Low
**Test Type**: Logging

**Steps**:
1. Call legacy endpoint
2. Check application logs for deprecation warning

**Expected Results**:
- Deprecation warning logged when legacy endpoint is called
- Warning indicates `/api/v1/products/dolar` is deprecated
- Warning suggests using `/api/v1/cotizacion/dolar` instead

**Actual Results**:
- Deprecation warning found: [ ] Yes [ ] No
- Warning message: __________

**Pass/Fail**: [ ] Pass [ ] Fail

---

## Expected Response Formats

### Success Responses

#### GET /api/v1/cotizacion/dolar - Success (200)
```json
{
  "valor": 1000.50
}
```

#### PUT /api/v1/cotizacion/dolar - Success (200)
```json
{
  "success": true
}
```

#### GET /api/v1/products/dolar - Success (200)
```json
{
  "valor": 1000.50
}
```

### Error Responses

#### 401 Unauthorized
```json
{
  "error": "Authentication required"
}
```

#### 403 Forbidden
```json
{
  "error": "Insufficient permissions"
}
```

#### 400 Bad Request - Missing valor
```json
{
  "error": "valor is required"
}
```

#### 400 Bad Request - Invalid valor (negative)
```json
{
  "error": "valor must be greater than 0"
}
```

#### 400 Bad Request - Invalid valor format
```json
{
  "error": "Invalid valor format"
}
```

---

## Test Execution Checklist

### Phase 4.1: GET Endpoint Testing
- [ ] TC-4.1.1: Unauthenticated access (401)
- [ ] TC-4.1.2: Authenticated access (200)
- [ ] TC-4.1.3: Response format validation

### Phase 4.2: PUT Endpoint Testing
- [ ] TC-4.2.1: Unauthenticated update (401)
- [ ] TC-4.2.2: Non-admin update (403)
- [ ] TC-4.2.3: Invalid rate - negative (400)
- [ ] TC-4.2.4: Invalid rate - zero (400)
- [ ] TC-4.2.5: Invalid rate - missing field (400)
- [ ] TC-4.2.6: Invalid rate - non-numeric (400)
- [ ] TC-4.2.7: Valid update (200)
- [ ] TC-4.2.8: Precision validation

### Phase 4.3: Admin UI Testing
- [ ] TC-4.3.1: Module loads and displays current rate
- [ ] TC-4.3.2: Save updates immediately
- [ ] TC-4.3.3: Error toast on invalid input
- [ ] TC-4.3.4: Error toast on network failure
- [ ] TC-4.3.5: UI accessibility and responsiveness

### Phase 4.4: Backwards Compatibility Testing
- [ ] TC-4.4.1: Legacy endpoint returns current rate
- [ ] TC-4.4.2: Legacy endpoint authentication
- [ ] TC-4.4.3: Deprecation warning logged

---

## Summary Statistics

**Total Test Cases**: 21
**Passed**: _____
**Failed**: _____
**Blocked**: _____
**Not Executed**: _____

### Pass/Fail by Phase
- Phase 4.1 (GET Endpoint): _____ / 3 passed
- Phase 4.2 (PUT Endpoint): _____ / 8 passed
- Phase 4.3 (Admin UI): _____ / 5 passed
- Phase 4.4 (Backwards Compatibility): _____ / 3 passed

### Critical Issues Found
1. __________
2. __________
3. __________

### High-Priority Issues Found
1. __________
2. __________
3. __________

---

## Notes and Observations

**Tester Name**: __________
**Test Date**: __________
**Environment**: Development [ ] | Staging [ ] | Production [ ]

**Additional Notes**:
- __________
- __________
- __________

---

## Sign-Off

**Phase 4 Testing**: [ ] Complete [ ] Incomplete

**Tester Approval**: __________

**Date**: __________
