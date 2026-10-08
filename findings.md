# Findings

## Database Design Notes
- The user specified that "citizin send his credintails like the phoen number and name and amount". This implies the transaction table should store `sender_name`, `sender_phone`, and `amount`.
- A `unique_reference_number` is required for each transaction.
- Status could be: `pending`, `accepted`, `rejected`.
- The system processes the transaction, then the Admin views it.

## Cleanup Tasks
- Need to remove Voucher-related views in `resources/views/customer`.
- Actually, since Citizens only use the API, we can delete the entire `customer` views folder, keeping only the `admin` views.
- Remove ResellerProfile model, Voucher model.
