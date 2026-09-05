<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'first_name', 'last_name', 'email', 'course', 'year_level', 'status'];

    public function attendances() { return $this->hasMany(Attendance::class); }
    public function getFullNameAttribute(): string { return "{$this->first_name} {$this->last_name}"; }
}
