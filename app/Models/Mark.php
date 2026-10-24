<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $guarded = [];
    public function exam() { return $this->belongsTo(Exam::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function subject() { return $this->belongsTo(Subject::class); }

    public static function grade(float $pct): string
    {
        return match (true) {
            $pct >= 90 => 'A+', $pct >= 80 => 'A', $pct >= 70 => 'B+',
            $pct >= 60 => 'B', $pct >= 50 => 'C', $pct >= 35 => 'D', default => 'F',
        };
    }
}
