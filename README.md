# Laravel E-commerce API

## Requirements
- PHP 8.1+
- Composer
- MySQL


## Setup
1. Clone repo
2. cp .env.example .env and set DB credentials and APP_URL
3. composer install
4. php artisan key:generate
5. php artisan jwt:secret
6. php artisan migrate
7. php artisan db:seed
8. php artisan serve

## API usage
- Register: POST /api/auth/register
  payload: {name, email, password, password_confirmation}
- Login: POST /api/auth/login
  payload: {email, password}
  returns token: store in Authorization: Bearer <token>
- All other endpoints require Authorization header.
- Products: GET/POST/PUT/DELETE /api/products
- Cart: GET /api/cart, POST /api/cart {product_id, quantity}, DELETE /api/cart/{id}
- Orders: POST /api/orders {address, phone} -> returns order_number, total, items

```mermaid
erDiagram
    users ||--o{ cart_items : has
    users ||--o{ orders : places
    products ||--o{ cart_items : referenced_in
    products ||--o{ order_items : referenced_in
    orders ||--o{ order_items : contains

    users {
      bigint id PK
      string name
      string email
      string password
    }
    products {
      bigint id PK
      string name
      text description
      decimal price
      int stock
    }
    cart_items {
      bigint id PK
      bigint user_id FK
      bigint product_id FK
      int quantity
    }
    orders {
      bigint id PK
      bigint user_id FK
      string order_number
      decimal total
      string address
      string phone
    }
    order_items {
      bigint id PK
      bigint order_id FK
      bigint product_id FK
      int quantity
      decimal unit_price
      decimal total_price
    }
```



