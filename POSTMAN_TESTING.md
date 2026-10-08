# Testing the Financial Transaction API with Postman

This guide provides step-by-step instructions on how to simulate a "Citizen" making a financial transaction using Postman, and how to verify that transaction as an "Admin" via the dashboard.

## 1. Start the Server

Before testing, make sure your local Laravel development server is running. Open a terminal in your project directory and run:

```bash
php artisan serve
```
By default, this runs on `http://localhost:8000`.

## 2. Setting Up the Postman Request

1. **Open Postman** and click **New > HTTP Request**.
2. **Set the HTTP Method** to `POST`.
3. **Set the URL** to: `http://localhost:8000/api/transactions`.

### 3. Configure Headers
We need to tell the API that we expect a JSON response.
1. Go to the **Headers** tab.
2. Add a new row:
   - **Key:** `Accept`
   - **Value:** `application/json`

### 4. Construct the Payload (Body)
This represents the credentials and details the citizen is sending.

1. Go to the **Body** tab.
2. Select **form-data** (or `x-www-form-urlencoded`).
3. Add the following key-value pairs:
   - **Key:** `sender_name` | **Value:** `Jane Doe`
   - **Key:** `sender_phone` | **Value:** `+1987654321`
   - **Key:** `amount` | **Value:** `1250.50`

*Note: The `amount` must be a numeric value greater than 0.*

## 5. Send the Request

Click the blue **Send** button.

### Expected Response

You should receive a `201 Created` status code, meaning the transaction was successfully stored in the system. The response body will look similar to this:

```json
{
    "message": "Transaction submitted successfully",
    "data": {
        "sender_name": "Jane Doe",
        "sender_phone": "+1987654321",
        "amount": "1250.50",
        "unique_reference_number": "TRX-K9M2P1Q8",
        "status": "pending",
        "updated_at": "2026-10-08T14:30:00.000000Z",
        "created_at": "2026-10-08T14:30:00.000000Z",
        "id": 1
    }
}
```

## 6. Verify in the Admin Dashboard

Now that the citizen has submitted a transaction, it needs to go through the Admin for approval.

1. Open your browser and go to `http://localhost:8000`.
2. **Log in** with the Admin credentials:
   - **Email:** `admin@connect.local`
   - **Password:** `password`
3. Once logged in, you will be redirected to the **Admin Dashboard**.
4. In the **Transactions** table, you will see `Jane Doe`'s request.
5. Notice that its status is currently **Pending**.
6. Click the green **Accept** or red **Reject** button.
7. The status will update, and the "Total Accepted Volume" metric at the top of the dashboard will recalculate accordingly.
