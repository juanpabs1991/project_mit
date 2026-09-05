<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Student;
use Livewire\Component;

class Dashboard extends Component
{
    public function render() {
        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i));
        $chart = $days->map(fn ($day) => ['label' => $day->format('D'), 'value' => Attendance::whereDate('attendance_date', $day)->count()]);
        $max = max(1, $chart->max('value'));
        return view('livewire.dashboard', [
            'totalStudents' => Student::where('status','active')->count(), 'presentToday' => Attendance::whereDate('attendance_date', today())->count(),
            'checkedOut' => Attendance::whereDate('attendance_date', today())->whereNotNull('time_out')->count(),
            'recent' => Attendance::with('student')->latest('time_in')->take(6)->get(), 'chart' => $chart, 'max' => $max,
        ])->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
