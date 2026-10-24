<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $guarded = [];
    protected $casts = ['start_date' => 'date'];
    public function schoolClass() { return $this->belongsTo(SchoolClass::class); }
    public function marks() { return $this->hasMany(Mark::class); }
}
