<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $table = 'school_classes';
    protected $guarded = [];
    public function students() { return $this->hasMany(Student::class); }
    public function getLabelAttribute(): string { return trim($this->name . ' ' . $this->section); }
}
