# POS Invoice Payment Improvements - Implementation Details

## Overview
This document describes the changes made to address the POS invoice payment issues reported by the customer.

## Issues Addressed

### 1. Arabic Numeral Display on Mobile
**Problem**: When entering payment amounts on mobile devices, numbers appeared as Arabic-Indic numerals (٢٠) instead of Western Arabic numerals (20).

**Solution**: Added JavaScript normalization in the payment dialog to convert any Arabic-Indic numerals (٠-٩) and Persian numerals (۰-۹) to Western Arabic numerals (0-9). This happens in two places:
- In the `append()` function when clicking number buttons
- In the `changes()` function for direct keyboard input

**Files Modified**:
- `resources/views/filament/tenant/pages/cashier.blade.php` (lines 484-514)

### 2. Custom Pricing for Flexibility
**Problem**: Customers wanted the ability to set custom prices per item (below or above the default selling price) to provide discounts or special pricing.

**Solution**: Implemented a comprehensive custom pricing system:

#### Database Changes
- Added `custom_unit_price` column to `cart_items` table
- Migration: `database/migrations/tenant/2024_10_16_123400_add_custom_unit_price_to_cart_items_table.php`

#### Model Changes
- Updated `CartItem` model to include `custom_unit_price` in fillable fields
- Added `getEffectiveUnitPriceAttribute()` method to calculate the effective unit price considering custom prices, price units, and default selling price

**Files Modified**:
- `app/Models/Tenants/CartItem.php`

#### UI Changes
- Added "Custom Price (Optional)" input field to the add-to-cart dialog
- Shows default selling price as placeholder and helper text
- Displays custom price indicator in cart when price differs from default

**Files Modified**:
- `app/Filament/Tenant/Pages/Traits/TableProduct.php` (lines 96-117)
- `resources/views/filament/tenant/pages/cashier.blade.php` (lines 76-85)

#### Logic Changes
- Updated `addCart()` method to accept and store custom prices
- Modified `reduceCart()` and `updateCart()` methods to preserve custom prices when adjusting quantities
- Ensured custom price is maintained when using the + button on existing cart items

**Files Modified**:
- `app/Filament/Tenant/Pages/Traits/CartInteraction.php`

#### Selling Details Integration
The existing system already supported custom prices in the selling flow:
- `Cashier::proceedThePayment()` passes `price` from cart items to the products array
- `AssignProduct` listener uses the provided price if available: `$productRequest['price'] ?? $product->selling_price * $productRequest['qty']`
- Selling details are created with the custom price

**No changes needed** - the integration works automatically!

### 3. Payment Validation
**Decision**: We did NOT remove the `payed_money >= total_price` validation. Instead, the custom pricing feature addresses the customer's need:
- Cashiers can set lower prices per item when adding to cart
- The total_price is calculated based on these custom prices
- Payment validation ensures the full (custom-priced) amount is paid
- This maintains data integrity while providing the requested flexibility

## Testing

### Manual Testing Steps

1. **Test Custom Pricing**:
   - Open POS page
   - Click "+" button on a product
   - Enter quantity and a custom price (e.g., 15000 instead of 20000)
   - Add to cart
   - Verify cart shows custom price indicator
   - Proceed to payment and complete transaction
   - Verify selling_details table has the custom price

2. **Test Arabic Numeral Normalization**:
   - Use a mobile device with Arabic locale
   - Open POS page
   - Add items to cart
   - Proceed to payment
   - Enter payment amount using the number pad
   - Verify numbers are displayed as 0-9 (not ٠-٩)

3. **Test Price Preservation**:
   - Add a product with custom price to cart
   - Click the "+" button on the same product
   - Verify the custom price is maintained (not reset to default)
   - Adjust quantity using the quantity input field
   - Verify the custom price is still maintained

### Automated Tests

Added two test cases in `tests/Feature/Http/Controllers/Api/Tenants/Transaction/SellingControllerTest.php`:

1. **test cashier can create the selling with custom price per item**
   - Tests that a custom price (15000) can be set for a product with default price of 20000
   - Verifies the selling and selling_details tables have the correct custom price

2. **test cashier can create the selling with custom price per item lower than cost**
   - Tests that a custom price can be set even below the product cost (at a loss)
   - Verifies system allows business decisions to sell at a loss for discounts

## Data Flow

1. User adds product to cart with custom price
2. `CartItem` stores: `custom_unit_price`, `price` (total), `qty`
3. Cart display shows custom price indicator if different from default
4. On payment, cart items are mapped to products array with `price` field
5. `SellingService` receives product data with custom prices
6. `AssignProduct` listener creates `SellingDetail` with custom price
7. Reports and calculations use the custom price from `selling_details`

## Backward Compatibility

- Existing cart items without custom prices continue to work (null values)
- Default behavior unchanged when no custom price is provided
- All existing selling records remain valid

## Security Considerations

- Custom prices can be set below cost (business decision for discounts)
- No minimum price validation enforced (allows maximum flexibility)
- Cashiers must have appropriate permissions to access POS
- Consider adding audit logging for significant price deviations (future enhancement)

## Future Enhancements

1. Add price change audit log
2. Add configurable min/max price limits
3. Add warnings for prices significantly below cost
4. Add bulk custom pricing for multiple items
5. Add custom price presets (wholesale, VIP, etc.)
