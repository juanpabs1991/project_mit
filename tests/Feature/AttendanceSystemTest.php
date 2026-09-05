<?php

namespace Tests\Feature;

use App\Livewire\Kiosk;
use App\Livewire\Students;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AttendanceSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_view_dashboard(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/dashboard');
        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('Attendance trend');
    }

    public function test_admin_can_add_a_student(): void
    {
        $this->actingAs(User::factory()->create());
        Livewire::test(Students::class)->set('student_id','TEST-001')->set('first_name','Ada')->set('last_name','Lovelace')
            ->set('email','ada@example.com')->set('course','Computer Science')->set('year_level',2)->call('save')->assertHasNoErrors();
        $this->assertDatabaseHas('students', ['student_id'=>'TEST-001']);
    }

    public function test_kiosk_records_time_in_then_time_out(): void
    {
        Student::create(['student_id'=>'TEST-002','first_name'=>'Grace','last_name'=>'Hopper','course'=>'Computer Science','year_level'=>3,'status'=>'active']);
        Livewire::test(Kiosk::class)->set('studentId','TEST-002')->call('submit')->assertSet('result.type','success')
            ->set('studentId','TEST-002')->call('submit')->assertSet('result.type','success');
        $this->assertNotNull(Attendance::first()->time_out);
    }
}
