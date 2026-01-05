<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Low Stock Alert</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #dc2626;">⚠️ Low Stock Alert</h2>
        
        <p>This is an automated notification to inform you that a product is running low on stock.</p>
        
        <div style="background-color: #f9fafb; border-left: 4px solid #dc2626; padding: 15px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #1f2937;">Product Details</h3>
            <p><strong>Product Name:</strong> {{ $product->name }}</p>
            <p><strong>SKU:</strong> {{ $product->sku }}</p>
            <p><strong>Current Stock:</strong> <span style="color: #dc2626; font-weight: bold;">{{ $product->stock_quantity }}</span></p>
            <p><strong>Low Stock Threshold:</strong> {{ $lowStockThreshold }}</p>
            @if($product->category)
                <p><strong>Category:</strong> {{ $product->category->name }}</p>
            @endif
        </div>
        
        <p style="margin-top: 20px;">
            Please consider restocking this product to avoid running out of inventory.
        </p>
        
        <p style="margin-top: 20px; color: #6b7280; font-size: 12px;">
            This is an automated message. Please do not reply to this email.
        </p>
    </div>
</body>
</html>
