<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasTimestamps, HasFactory;

    protected $table = 'orders';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'items',
        'user_id',
        'total_price',
        'quantity',
        'status',
        'payment_method',
    ];

    protected $casts = [
        'items' => 'array',
        'total_price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
