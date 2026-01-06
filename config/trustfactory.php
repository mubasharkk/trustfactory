<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Low Stock Threshold
    |--------------------------------------------------------------------------
    |
    | The minimum stock quantity before a low stock notification is triggered.
    | When a product's stock_quantity falls below this value, an email
    | notification will be sent to the admin email.
    |
    */

    'low_stock_threshold' => env('PRODUCT_LOW_STOCK_THRESHOLD', 10),

    /*
    |--------------------------------------------------------------------------
    | Admin Email
    |--------------------------------------------------------------------------
    |
    | The email address that will receive low stock notifications.
    |
    */

    'admin_email' => env('PRODUCT_ADMIN_EMAIL', 'admin@example.com'),

    /*
    |--------------------------------------------------------------------------
    | Daily Sales Report Time
    |--------------------------------------------------------------------------
    |
    | The time when the daily sales report job should run.
    | Format: HH:MM (24-hour format, e.g., '20:00' for 8:00 PM)
    |
    */

    'daily_sales_report_time' => env('DAILY_SALES_REPORT_TIME', '20:00'),
];
