<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionDetail extends Model
{
    protected $guarded = ['id'];
    
    public function transaction():BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_invoice_code', 'invoice_code');
    }

    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }
}
