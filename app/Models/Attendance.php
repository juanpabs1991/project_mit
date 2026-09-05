<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'attendance_date', 'time_in', 'time_out', 'status'];
    protected function casts(): array { return ['attendance_date' => 'date', 'time_in' => 'datetime', 'time_out' => 'datetime']; }
    public function student() { return $this->belongsTo(Student::class); }
}
