<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherCombination extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'voucher_combinations';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'voucher_a_id',
        'voucher_b_id',
        'is_allowed',
        'rule_description',
    ];

    protected function casts(): array
    {
        return [
            'is_allowed' => 'boolean',
        ];
    }

    public function voucherA()
    {
        return $this->belongsTo(Voucher::class, 'voucher_a_id');
    }

    public function voucherB()
    {
        return $this->belongsTo(Voucher::class, 'voucher_b_id');
    }
}
