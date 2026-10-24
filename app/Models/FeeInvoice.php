<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FeeInvoice extends Model
{
    protected $guarded = [];
    protected $casts = ['due_date' => 'date'];

    public function student() { return $this->belongsTo(Student::class); }
    public function structure() { return $this->belongsTo(FeeStructure::class, 'fee_structure_id'); }
    public function payments() { return $this->hasMany(Payment::class); }

    public function paid(): float { return (float) $this->payments()->sum('amount'); }
    public function balance(): float { return round($this->amount - $this->paid(), 2); }

    public function refreshStatus(): void
    {
        $paid = $this->paid();
        $this->status = $paid >= $this->amount ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
        $this->save();
    }
}
