<?php

namespace App\Livewire;

use App\Models\Attendance;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceLogs extends Component
{
    use WithPagination; public string $search='', $date='';
    public function updated($name) { if(in_array($name,['search','date'])) $this->resetPage(); }
    public function render() { $logs=Attendance::with('student')->when($this->date,fn($q)=>$q->whereDate('attendance_date',$this->date))->whereHas('student',fn($q)=>$q->where('student_id','like',"%{$this->search}%")->orWhere('first_name','like',"%{$this->search}%")->orWhere('last_name','like',"%{$this->search}%"))->latest('attendance_date')->latest('time_in')->paginate(12); return view('livewire.attendance-logs',compact('logs'))->layout('layouts.app',['title'=>'Attendance Logs']); }
}
