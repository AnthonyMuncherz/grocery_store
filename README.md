# Malaysian Grocery Store

A vanilla PHP-based e-commerce system for a Malaysian grocery store, built with SQLite3 database. This version is styled with **Tailwind CSS**.

## Features

- Product browsing and search
- Shopping cart functionality
- Order management
- Malaysian payment processing (dummy implementation)
- Inventory management
- Responsive design with Tailwind CSS
- Malaysian localization (currency, phone numbers, addresses)

## Requirements

- PHP 8.0 or higher
- SQLite3 extension enabled
- Modern web browser
- Laragon (for local development)
- Internet connection (for Tailwind CSS CDN)

## Installation

1. Clone the repository to your Laragon's www directory:
```bash
cd C:\laragon\www
git clone [repository-url] grocery_store
```

2. Ensure SQLite3 extension is enabled in your PHP configuration:
```ini
extension=sqlite3
```

3. Set appropriate permissions for the database directory:
```bash
chmod 755 database/
chmod 644 database/grocery_store.db
```

4. Create required directories for file uploads and logs:
```bash
mkdir -p assets/images/products
mkdir -p logs
```

5. Set write permissions for upload and log directories:
```bash
chmod 755 assets/images/products
chmod 755 logs
```

## Directory Structure

```
grocery_store/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── config/
│   └── database.php
├── includes/
│   ├── functions.php
│   └── constants.php
├── modules/
│   ├── products/
│   ├── orders/
│   ├── payments/
│   └── inventory/
├── database/
│   └── grocery_store.db
└── templates/
    ├── header.php
    └── footer.php
```

## Module Structure

Each module follows a consistent structure:

```
module_name/
├── index.php
├── functions.php
��── config.php
��── templates/
    ├── list.php
    ├── view.php
    ├── add.php
    └── edit.php
```

## Database Schema

The application uses SQLite3 with the following main tables:

1. products
   - Product information and inventory
2. orders
   - Order details and status
3. order_items
   - Items within each order
4. inventory_logs
   - Stock movement tracking

## Payment Methods

The system supports simulation of common Malaysian payment methods:

1. FPX (Financial Process Exchange)
   - Major Malaysian banks
2. Credit/Debit Cards
   - Visa
   - Mastercard
   - Local bank cards
3. E-wallets
   - Touch 'n Go eWallet
   - Boost
   - GrabPay
   - MAE by Maybank

## Development Guidelines

1. Code Style
   - Follow PSR-12 coding standards
   - Use meaningful variable and function names
   - Comment complex logic
   - Keep functions small and focused

2. Security
   - Validate all input
   - Use prepared statements
   - Implement CSRF protection
   - Sanitize output

3. Error Handling
   - Log all errors appropriately
   - Display user-friendly error messages
   - Maintain separate logs for different error types

## Testing

1. Test Scenarios
   - Product CRUD operations
   - Order processing flow
   - Payment processing
   - Inventory management
   - Input validation
   - Error handling

2. Test Data
   - Use realistic Malaysian product names
   - Use valid Malaysian phone numbers
   - Use valid Malaysian addresses
   - Use realistic MYR prices

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please email support@grocerystore.my or create an issue in the repository.