# Task Plan: Financial Transaction System Overhaul

## Goal
Refactor the existing "Voucher Transaction" system into a "Financial Transaction" system with two main roles (Admin, Citizen), where Citizens submit transactions via API and Admins approve/reject them via the UI dashboard.

## Current Phase
Phase 1

## Phases

### Phase 1: Planning and Documentation
- [x] Initialize Manus-style planning files (task_plan, findings, progress).
- [x] Update README.md and COMPLETE_PROJECT_GUIDE.md to reflect the new architecture.
- **Status:** complete

### Phase 2: Database Overhaul
- [x] Remove `reseller_profiles` and `vouchers` migrations/tables.
- [x] Ensure `users` table handles Admin and Citizen roles properly (can be a simple `role` or existing `is_admin` structure).
- [x] Create `transactions` migration (amount, sender_name, sender_phone, unique_reference_number, status, admin_id for action).
- **Status:** complete

### Phase 3: Models and Logic
- [x] Delete `ResellerProfile` and `Voucher` models.
- [x] Create `Transaction` model.
- [x] Setup relationships (Transaction belongs to Admin).
- **Status:** complete

### Phase 4: API Development
- [x] Create `routes/api.php` if not exists, and define `POST /api/transactions`.
- [x] Create `Api\TransactionController` to handle incoming citizen requests.
- [x] Generate unique reference number automatically upon creation.
- **Status:** complete

### Phase 5: Admin UI Overhaul
- [ ] Update Admin Dashboard to list `transactions` instead of vouchers/resellers.
- [ ] Add Accept and Reject buttons.
- [ ] Update routes and controllers for admin actions.
- **Status:** in_progress

### Phase 6: Postman Testing Instructions
- [x] Document how to test the API with Postman.
- **Status:** complete

## Postman Testing Instructions
1. Run `php artisan serve` to start the local server.
2. Open Postman and create a new **POST** request.
3. Enter the URL: `http://localhost:8000/api/transactions`.
4. Go to the **Headers** tab and add:
   - `Accept`: `application/json`
5. Go to the **Body** tab, select **form-data** or **x-www-form-urlencoded**, and add the following keys and values:
   - `sender_name`: e.g., `John Doe`
   - `sender_phone`: e.g., `+1234567890`
   - `amount`: e.g., `500.00`
6. Click **Send**.
7. The API will respond with `201 Created` and return the transaction details, including the generated `unique_reference_number`.
8. Log in to the Admin Dashboard (`admin@connect.local` / `password`), and you will see the new pending transaction ready for Accept/Reject.

## Key Questions
1. Does the Citizen need a persistent User account in the database, or is the transaction just recording the sender's name and phone number as text fields? -> Assuming text fields on the transaction based on the prompt ("citzin send his credintails like the phoen number and name and amount"), but I'll make sure it's robust.
2. Are we keeping the `users` table for Admins? Yes, Admin logs in via the existing UI.

## Decisions Made
| Decision | Rationale |
|----------|-----------|
|          |           |

## Errors Encountered
| Error | Attempt | Resolution |
|-------|---------|------------|
|       |         |            |

## Notes
- Ensure unique reference number is generated on transaction creation.
