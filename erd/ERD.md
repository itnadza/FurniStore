```mermaid
erDiagram
    %% ===================================================
    %% FULL DATABASE SCHEMA FOR FURNI STORE
    %% ===================================================

    %% 1. USERS table
    USERS {
        int user_id PK          
        string name
        string email
        string password
        string phone(OPTIONAL)
        string address(OPTIONAL)
        string role             %% "admin" or "client"
    }

    %% 2. PRODUCTS table
    PRODUCTS {
        int product_id PK
        string name
        float price
        string image_url
        int stock_quantity
        string description
    }

    %% 3. CART_ITEMS table
    CART_ITEMS {
        int cart_item_id PK
        int user_id FK
        int product_id FK
        int quantity
    }

    %% 4. ORDERS table
    ORDERS {
        int order_id PK
        int user_id FK
        datetime order_date
        float total_amount
        string status
        %% Checkout: CART_ITEMS -> ORDERS + ORDER_ITEMS + PAYMENTS
    }

    %% 5. ORDER_ITEMS table
    ORDER_ITEMS {
        int order_item_id PK
        int order_id FK
        int product_id FK
        int quantity
        float price
    }

    %% 6. PAYMENTS table
    PAYMENTS {
        int payment_id PK
        int order_id FK
        float amount
        string payment_method
        string payment_status
        datetime payment_date
    }

    
    %% Relationships 
    

    %% 1. USERS -> CART_ITEMS
    USERS ||--o{ CART_ITEMS : One user can have multiple items in their cart. Each cart item belongs to exactly one user.

    %% 2. USERS -> ORDERS
    USERS ||--o{ ORDERS : One user can place multiple orders. Each order belongs to exactly one user.

    %% 3. ORDERS -> ORDER_ITEMS
    ORDERS ||--o{ ORDER_ITEMS : One order can contain multiple order items (products). Each order item belongs to exactly one order.

    %% 4. PRODUCTS -> ORDER_ITEMS
    PRODUCTS ||--o{ ORDER_ITEMS : One product can appear in multiple order items (in different orders). Each order item corresponds to exactly one product.

    %% 5. PRODUCTS -> CART_ITEMS
    PRODUCTS ||--o{ CART_ITEMS : One product can be added to multiple users’ carts. Each cart item corresponds to exactly one product.

    %% 6. ORDERS -> PAYMENTS
    ORDERS ||--o{ PAYMENTS : One order can have one or more payments. Each payment is linked to exactly one order.




    %% Checkout Flow 
    CART_ITEMS -->|checkout| ORDERS
    ORDERS -->|generates| ORDER_ITEMS
    ORDERS -->|payment| PAYMENTS

   