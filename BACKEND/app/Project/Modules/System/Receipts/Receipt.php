<?php

namespace App\Project\Modules\System\Receipts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\BankDetails\BankDetail;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\Core\PaymentModes\PaymentMode;
use App\Project\Modules\System\Invoices\Invoice;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "receipts";

    protected $fillable = [
        'reference_number',
        'invoice_id',
        'amount',
        'currency_id',
        'receipt_date',
        'bank_detail_id',
        'payment_mode_id',
        'cheque_number',
        'cheque_date',
        'remarks',
        'created_at',
        'deleted_at',
        'updated_at',
        'created_by',
        'updated_by',
        'uuid'
    ];

    public function bankDetail()
    {
        return $this->belongsTo(BankDetail::class, 'bank_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getCreatedAtAttribute($value)
    {
        return $this->attributes['created_at'] = (new Carbon($value))->toDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return $this->attributes['updated_at'] = (new Carbon($value))->toDateTimeString();
    }

    public function getDeletedAtAttribute($value)
    {
        return $this->attributes['deleted_at'] = (new Carbon($value))->toDateTimeString();
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::orderedUuid();
            $model->created_at = Carbon::now();
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now();
        });
    }
}
