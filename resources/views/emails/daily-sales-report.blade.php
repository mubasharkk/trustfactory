<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Sales Report</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #1f2937; border-bottom: 3px solid #3b82f6; padding-bottom: 10px;">
            📊 Daily Sales Report
        </h2>
        
        <div style="background-color: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0;">
            <p style="margin: 0; font-size: 16px; font-weight: bold;">
                Report Date: {{ $reportDate->format('F j, Y') }}
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 30px 0;">
            <div style="background-color: #eff6ff; padding: 20px; border-radius: 8px; text-align: center;">
                <div style="font-size: 32px; font-weight: bold; color: #3b82f6; margin-bottom: 5px;">
                    {{ $totalOrders }}
                </div>
                <div style="color: #6b7280; font-size: 14px;">Total Orders</div>
            </div>
            
            <div style="background-color: #f0fdf4; padding: 20px; border-radius: 8px; text-align: center;">
                <div style="font-size: 32px; font-weight: bold; color: #22c55e; margin-bottom: 5px;">
                    ${{ number_format($totalRevenue, 2) }}
                </div>
                <div style="color: #6b7280; font-size: 14px;">Total Revenue</div>
            </div>
            
            <div style="background-color: #fef3c7; padding: 20px; border-radius: 8px; text-align: center;">
                <div style="font-size: 32px; font-weight: bold; color: #f59e0b; margin-bottom: 5px;">
                    {{ $totalQuantity }}
                </div>
                <div style="color: #6b7280; font-size: 14px;">Items Sold</div>
            </div>
        </div>

        @if(count($productsSold) > 0)
            <h3 style="color: #1f2937; margin-top: 30px; margin-bottom: 15px;">Products Sold</h3>
            
            <table style="width: 100%; border-collapse: collapse; background-color: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <thead>
                    <tr style="background-color: #f9fafb;">
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb; color: #374151; font-weight: 600;">Product Name</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e5e7eb; color: #374151; font-weight: 600;">Quantity</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid #e5e7eb; color: #374151; font-weight: 600;">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productsSold as $product)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px; color: #1f2937;">{{ $product['product_name'] }}</td>
                            <td style="padding: 12px; text-align: right; color: #6b7280;">{{ $product['quantity'] }}</td>
                            <td style="padding: 12px; text-align: right; color: #22c55e; font-weight: 600;">${{ number_format($product['revenue'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f9fafb; font-weight: bold;">
                        <td style="padding: 12px; border-top: 2px solid #e5e7eb; color: #1f2937;">Total</td>
                        <td style="padding: 12px; text-align: right; border-top: 2px solid #e5e7eb; color: #1f2937;">{{ $totalQuantity }}</td>
                        <td style="padding: 12px; text-align: right; border-top: 2px solid #e5e7eb; color: #22c55e;">${{ number_format($totalRevenue, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0;">
                <p style="margin: 0; color: #92400e;">
                    No orders were placed on {{ $reportDate->format('F j, Y') }}.
                </p>
            </div>
        @endif

        <p style="margin-top: 30px; color: #6b7280; font-size: 12px; border-top: 1px solid #e5e7eb; padding-top: 15px;">
            This is an automated daily sales report. Generated on {{ now()->format('F j, Y \a\t g:i A') }}.
        </p>
    </div>
</body>
</html>
