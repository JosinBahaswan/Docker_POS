<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Transaction extends Model
{
    protected $guarded = ['id'];
    public function getRouteKeyName():string
    {
        return 'invoice_code';
    }

    public function details():HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_invoice_code', 'invoice_code');
    }
}
