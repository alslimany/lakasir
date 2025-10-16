<?php

use App\Models\Tenants\Product;
use App\Models\Tenants\Selling;
use App\Models\Tenants\SellingDetail;
use App\Models\Tenants\Stock;
use App\Models\Tenants\User;
use Tests\RefreshDatabaseWithTenant;

use function Pest\Laravel\actingAs;

uses(RefreshDatabaseWithTenant::class);

test('deleting a selling restores product stock', function () {
    $user = User::first();
    
    // Create a product with initial stock of 10
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'initial_price' => 10000,
        'selling_price' => 20000,
        'stock' => 10,
        'is_non_stock' => false,
    ]);
    
    // Create a stock entry for FIFO/LIFO tracking
    Stock::factory()->createQuietly([
        'product_id' => $product->id,
        'stock' => 10,
        'init_stock' => 10,
        'type' => 'in',
        'date' => now(),
    ]);
    
    // Create a selling with 3 items
    $selling = Selling::factory()->create([
        'user_id' => $user->id,
        'total_price' => 60000,
        'total_cost' => 30000,
        'total_qty' => 3,
    ]);
    
    SellingDetail::factory()->create([
        'selling_id' => $selling->id,
        'product_id' => $product->id,
        'qty' => 3,
        'price' => 60000,
        'cost' => 30000,
    ]);
    
    // Manually reduce stock to simulate the sale (normally done by listener)
    $stock = Stock::where('product_id', $product->id)->first();
    $stock->stock = 7; // 10 - 3
    $stock->save();
    
    // Verify stock was reduced
    $stock->refresh();
    expect($stock->stock)->toBe(7);
    
    // Delete the selling (should restore stock)
    actingAs($user);
    $selling->delete();
    
    // Verify stock was restored
    $stock->refresh();
    expect($stock->stock)->toBe(10); // Should be back to 10
});

test('deleting a selling with multiple products restores all product stocks', function () {
    $user = User::first();
    
    // Create two products with initial stock
    $product1 = Product::factory()->create([
        'name' => 'Product 1',
        'initial_price' => 10000,
        'selling_price' => 20000,
        'stock' => 12,
        'is_non_stock' => false,
    ]);
    
    $product2 = Product::factory()->create([
        'name' => 'Product 2',
        'initial_price' => 15000,
        'selling_price' => 25000,
        'stock' => 8,
        'is_non_stock' => false,
    ]);
    
    // Create stock entries
    Stock::factory()->createQuietly([
        'product_id' => $product1->id,
        'stock' => 12,
        'init_stock' => 12,
        'type' => 'in',
        'date' => now(),
    ]);
    
    Stock::factory()->createQuietly([
        'product_id' => $product2->id,
        'stock' => 8,
        'init_stock' => 8,
        'type' => 'in',
        'date' => now(),
    ]);
    
    // Create a selling with both products
    $selling = Selling::factory()->create([
        'user_id' => $user->id,
        'total_price' => 90000,
        'total_cost' => 50000,
        'total_qty' => 4,
    ]);
    
    SellingDetail::factory()->create([
        'selling_id' => $selling->id,
        'product_id' => $product1->id,
        'qty' => 2,
        'price' => 40000,
        'cost' => 20000,
    ]);
    
    SellingDetail::factory()->create([
        'selling_id' => $selling->id,
        'product_id' => $product2->id,
        'qty' => 2,
        'price' => 50000,
        'cost' => 30000,
    ]);
    
    // Manually reduce stocks
    $stock1 = Stock::where('product_id', $product1->id)->first();
    $stock1->stock = 10; // 12 - 2
    $stock1->save();
    
    $stock2 = Stock::where('product_id', $product2->id)->first();
    $stock2->stock = 6; // 8 - 2
    $stock2->save();
    
    // Verify stocks were reduced
    $stock1->refresh();
    $stock2->refresh();
    expect($stock1->stock)->toBe(10);
    expect($stock2->stock)->toBe(6);
    
    // Delete the selling (should restore stocks)
    actingAs($user);
    $selling->delete();
    
    // Verify stocks were restored
    $stock1->refresh();
    $stock2->refresh();
    expect($stock1->stock)->toBe(12); // Should be back to 12
    expect($stock2->stock)->toBe(8);  // Should be back to 8
});

test('deleting a selling with non-stock product does not affect stock', function () {
    $user = User::first();
    
    // Create a non-stock product
    $product = Product::factory()->create([
        'name' => 'Service Product',
        'initial_price' => 10000,
        'selling_price' => 20000,
        'stock' => 0,
        'is_non_stock' => true,
    ]);
    
    // Create a selling
    $selling = Selling::factory()->create([
        'user_id' => $user->id,
        'total_price' => 20000,
        'total_cost' => 10000,
        'total_qty' => 1,
    ]);
    
    SellingDetail::factory()->create([
        'selling_id' => $selling->id,
        'product_id' => $product->id,
        'qty' => 1,
        'price' => 20000,
        'cost' => 10000,
    ]);
    
    // Verify product stock is 0
    $product->refresh();
    expect($product->stock)->toBe(0);
    
    // Delete the selling (should not affect stock for non-stock product)
    actingAs($user);
    $selling->delete();
    
    // Verify stock is still 0
    $product->refresh();
    expect($product->stock)->toBe(0);
});
