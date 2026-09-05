<?php

namespace App\Livewire;

use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class Students extends Component
{
    use WithPagination;
    public string $search = '', $student_id = '', $first_name = '', $last_name = '', $email = '', $course = '', $status = 'active';
    public int $year_level = 1; public ?int $editingId = null; public bool $showForm = false;
    public function updatedSearch() { $this->resetPage(); }
    protected function rules() { return ['student_id'=>'required|max:30|unique:students,student_id,'.($this->editingId ?? 'NULL'),'first_name'=>'required|max:80','last_name'=>'required|max:80','email'=>'nullable|email|unique:students,email,'.($this->editingId ?? 'NULL'),'course'=>'required|max:100','year_level'=>'required|integer|min:1|max:6','status'=>'required|in:active,inactive']; }
    public function create() { $this->resetForm(); $this->showForm = true; }
    public function edit(int $id) { $s=Student::findOrFail($id); foreach (['student_id','first_name','last_name','email','course','year_level','status'] as $f) $this->$f=$s->$f ?? ''; $this->editingId=$id; $this->showForm=true; }
    public function save() { $data=$this->validate(); Student::updateOrCreate(['id'=>$this->editingId],$data); session()->flash('success',$this->editingId?'Student updated.':'Student added.'); $this->resetForm(); }
    public function delete(int $id) { Student::findOrFail($id)->delete(); session()->flash('success','Student deleted.'); }
    public function resetForm() { $this->reset(['student_id','first_name','last_name','email','course','editingId','showForm']); $this->year_level=1; $this->status='active'; $this->resetValidation(); }
    public function render() { $students=Student::where(fn($q)=>$q->where('student_id','like',"%{$this->search}%")->orWhere('first_name','like',"%{$this->search}%")->orWhere('last_name','like',"%{$this->search}%"))->latest()->paginate(8); return view('livewire.students',compact('students'))->layout('layouts.app',['title'=>'Students']); }
}
