<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'members_id',
        'cashiers_id',
        'total_price'

    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'members_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'transactions_id', 'id');
    }

    public function cashier()
    {
        return $this->belongsTo(Cashier::class, 'cashiers_id', 'id');
    }
}
