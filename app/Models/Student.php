<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $guarded = [];
    protected $casts = ['dob' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }
    public function invoices() { return $this->hasMany(FeeInvoice::class); }
    public function marks() { return $this->hasMany(Mark::class); }

    public function attendancePercent(): ?float
    {
        $total = $this->attendances()->count();
        if (!$total) return null;
        $ok = $this->attendances()->whereIn('status', ['present', 'late'])->count();
        return round($ok / $total * 100, 1);
    }
}
