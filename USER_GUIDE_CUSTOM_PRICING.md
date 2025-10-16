# Custom Pricing Feature - User Guide

## Overview
The POS system now supports custom pricing per item, allowing cashiers to set special prices for individual products during checkout. This feature provides flexibility for discounts, special promotions, or customer-specific pricing.

## How to Use Custom Pricing

### Step 1: Browse Products
- Navigate to the POS page
- Browse or search for products in the product grid

### Step 2: Add Product with Custom Price
1. Click the **"+ Insert Amount"** button on a product
2. A dialog will appear with two fields:
   - **Amount**: Enter the quantity (default: 1)
   - **Custom Price (Optional)**: Enter the custom unit price
     - Leave blank to use the default selling price
     - Enter a custom price to override the default
     - The default price is shown as a placeholder and hint

### Step 3: Review in Cart
- The product appears in the cart on the right side
- If a custom price was set:
  - A **"Custom price"** indicator shows below the product name
  - The custom unit price is displayed
  - The total price reflects the custom price × quantity

### Step 4: Adjust Quantity
- Use the **+** button to increase quantity
  - Custom price is preserved
- Use the **-** button to decrease quantity
  - Custom price is preserved
- Enter a quantity directly in the input field
  - Custom price is preserved

### Step 5: Complete Transaction
1. Click **"Proceed to payment"**
2. Enter payment details
3. The total reflects all custom prices
4. Complete the transaction

## Examples

### Example 1: Discount for VIP Customer
- Product: Premium Tea
- Default Price: $20,000
- Custom Price: $15,000 (25% discount)
- Quantity: 2
- **Total: $30,000** (instead of $40,000)

### Example 2: Clearance Sale
- Product: Seasonal Item
- Default Price: $50,000
- Custom Price: $25,000 (50% discount)
- Quantity: 5
- **Total: $125,000** (instead of $250,000)

### Example 3: Bundle Pricing
- Product: Coffee Beans
- Default Price: $100,000/kg
- Custom Price: $80,000/kg (bulk discount)
- Quantity: 10
- **Total: $800,000** (instead of $1,000,000)

## Important Notes

### Custom Price vs Discount
- **Custom Price**: Sets a new unit price for the item
- **Discount Field**: Applies an additional discount on top of the price
- Both can be used together for maximum flexibility

### Data Integrity
- Custom prices are saved in the database
- Reports accurately reflect custom prices
- Cost calculations remain accurate
- Profit margins are calculated based on custom prices

### Business Considerations
- Custom prices can be set **below cost** if needed
  - Useful for clearance or promotional sales
  - System allows business decisions for strategic pricing
- Consider training cashiers on pricing policies
- Monitor custom pricing usage through reports

### Mobile Usage
- The feature works seamlessly on mobile devices
- Number input has been fixed to show Western Arabic numerals (0-9)
- Touch-friendly interface for easy data entry

## Frequently Asked Questions

### Q: What happens if I don't enter a custom price?
**A**: The product uses its default selling price.

### Q: Can I change the custom price after adding to cart?
**A**: Currently, you need to remove the item and add it again with a new price. Alternatively, you can use the discount field for adjustments.

### Q: Is there a minimum or maximum custom price?
**A**: No enforced limits. You can set any price, including below cost if needed.

### Q: Will custom prices affect my inventory reports?
**A**: No. Inventory is tracked by quantity. Custom prices only affect sales amounts and profit calculations.

### Q: Can I set different prices for different customers for the same product?
**A**: Yes! Simply set the appropriate custom price when adding to each customer's cart.

### Q: What if I accidentally set a wrong price?
**A**: Remove the item from cart and add it again with the correct price before completing the transaction.

## Tips for Cashiers

1. **Verify Prices**: Always double-check custom prices before proceeding to payment
2. **Use Consistently**: Follow store policies for when to use custom pricing
3. **Document Reasons**: Consider adding notes to transactions explaining significant price changes
4. **Monitor Cart**: The cart display clearly shows which items have custom prices
5. **Quick Discounts**: For simple discounts, use the discount field instead of custom pricing

## Technical Details

### Data Storage
- Custom unit prices are stored in the cart_items table
- Final prices are saved in selling_details table
- Original product prices remain unchanged

### Compatibility
- Works with all product types
- Compatible with tax calculations
- Compatible with voucher systems
- Compatible with member discounts

### Performance
- No impact on system performance
- Efficient database queries
- Real-time price calculations
