<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'transactions_id',
        'services_id',
        'weight',
        'sub_price',

    ];


    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'services_id');
    }
}
