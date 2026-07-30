<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance', 'held_amount', 'credit_limit', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function availableBalance()
    {
        return $this->balance - $this->held_amount;
    }

    public function holdAmount($amount, $description = null, $referenceId = null, $jobId = null)
    {
        if ($this->availableBalance() < $amount) {
            throw new \Exception('الرصيد المتاح غير كافٍ');
        }

        $this->held_amount += $amount;
        $this->save();

        $this->transactions()->create([
            'type' => 'fee_hold',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'job_id' => $jobId,
            'description' => $description,
            'reference_id' => $referenceId,
        ]);
    }

    public function confirmHold($amount, $description = null, $referenceId = null, $jobId = null)
    {
        $this->held_amount -= $amount;
        $this->balance -= $amount;
        $this->save();

        $this->transactions()->create([
            'type' => 'withdrawal',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'job_id' => $jobId,
            'description' => $description,
            'reference_id' => $referenceId,
        ]);
    }

    public function releaseHold($amount, $description = null, $referenceId = null, $jobId = null)
    {
        $this->held_amount -= $amount;
        $this->save();

        $this->transactions()->create([
            'type' => 'refund',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'job_id' => $jobId,
            'description' => $description,
            'reference_id' => $referenceId,
        ]);
    }

    public function hasReachedCreditLimit()
    {
        return $this->balance <= $this->credit_limit;
    }
}