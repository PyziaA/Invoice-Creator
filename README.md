
# Invoice-Creator 


## Installation

Clone the repository
```bash
git clone git@github.com:PyziaA/Invoice-Creator.git
```
Switch to the project folder
```bash
cd Invoice-Creator
```
Install all the dependencies using composer
```bash
composer install
```   
Copy the example env file and make the required configuration changes in the .env file
```bash
cp .env.example .env
```  
Generate a new application key
```bash
php artisan key:generate
```  
Generate a new JWT authentication secret key
```bash
php artisan jwt:generate
```  
Run the database migrations (Set the database connection in .env before migrating)
```bash
php artisan migrate
```
Start the local development server
```bash
php artisan serve
```
## Dependencies
- [jwt-auth](https://github.com/tymondesigns/jwt-auth) - For authentication using JSON Web Tokens
- [laravel-dompdf](https://github.com/barryvdh/laravel-dompdf) - DOMPDF Wrapper for Laravel
- [number-to-words](https://github.com/kwn/number-to-words) - PHP Number to words converter
## Endpoints
***USER***

**User login** ```POST /auth/login```

**User register** ```POST /auth/register```

**User logout** ```POST /auth/logout```

**Refresh jwt token** ```POST /auth/refresh```

**Get user profile** ```GET /auth/user-profile```

***CUSTOMER***

**Add customer** ```POST /customer/store```

**Get customers** ```GET /customer/```

**Show customer** ```GET /customer/{id}```

**Update customer** ```PUT /customer/{id}```

**Delete customer** ```DELETE /customer/{id}```

***INVOICE***

**Add invoice** ```POST /invoices/```

**Get invoices** ```GET /invoices/```

**Show invoice** ```GET /invoices/{id}```

**Update invoice** ```PUT /invoices/{id}```

**Delete invoice** ```DELETE /invoices/{id}```

**Get invoice in pdf format** ```GET /invoices/pdf/{id}```

## Authentication
There are a number of ways to send the token via http:

**Authorization header**

```Authorization: Bearer eyJhbGciOiJIUzI1NiI...```

**Query string parameter**


```http://example.dev/me?token=eyJhbGciOiJIUzI1NiI...```