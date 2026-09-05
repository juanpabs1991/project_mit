<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Student;
use Livewire\Component;

class Kiosk extends Component
{
    public string $studentId = ''; public ?array $result = null; public int $resultKey = 0;
    public function submit() {
        $this->validate(['studentId'=>'required']); $student=Student::where('student_id',trim($this->studentId))->where('status','active')->first();
        if (!$student) { $this->resultKey++; $this->result=['type'=>'error','title'=>'Student not found','message'=>'Check the ID and try again.']; return; }
        $attendance=Attendance::where('student_id',$student->id)->whereDate('attendance_date',today())->first();
        if (!$attendance) { Attendance::create(['student_id'=>$student->id,'attendance_date'=>today(),'time_in'=>now(),'status'=>now()->format('H:i')>'08:15'?'late':'present']); $action='Time in recorded'; $animation='time-in'; $greeting='Welcome'; }
        elseif (!$attendance->time_out) { $attendance->update(['time_out'=>now()]); $action='Time out recorded'; $animation='time-out'; $greeting='See you again'; }
        else { $this->resultKey++; $this->result=['type'=>'error','title'=>'Attendance complete','message'=>"{$student->full_name} has already timed in and out today."]; $this->studentId=''; return; }
        $this->resultKey++;
        $this->result=['type'=>'success','animation'=>$animation,'title'=>$action,'name'=>$student->full_name,'message'=>"{$greeting}, {$student->full_name} · ".now()->format('h:i A')]; $this->studentId='';
    }
    public function render() { return view('livewire.kiosk',['todayCount'=>Attendance::whereDate('attendance_date',today())->count(),'recent'=>Attendance::with('student')->whereDate('attendance_date',today())->latest('updated_at')->take(5)->get()])->layout('layouts.kiosk',['title'=>'Attendance Kiosk']); }
}
