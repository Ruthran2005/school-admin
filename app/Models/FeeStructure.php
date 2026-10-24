<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $guarded = [];
    protected $casts = ['due_date' => 'date'];
    public function schoolClass() { return $this->belongsTo(SchoolClass::class); }
    public function invoices() { return $this->hasMany(FeeInvoice::class); }
}
