<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
        $students = collect([
            ['2026-0001','Maya','Santos','BS Information Technology',2], ['2026-0002','Liam','Reyes','BS Computer Science',3],
            ['2026-0003','Sofia','Cruz','BS Business Administration',1], ['2026-0004','Noah','Garcia','BS Information Systems',4],
            ['2026-0005','Emma','Flores','BS Computer Science',2], ['2026-0006','Lucas','Mendoza','BS Information Technology',1],
        ])->map(fn($s)=>Student::create(['student_id'=>$s[0],'first_name'=>$s[1],'last_name'=>$s[2],'email'=>strtolower($s[1].'.'.$s[2]).'@school.edu','course'=>$s[3],'year_level'=>$s[4],'status'=>'active']));
        foreach (range(0, 6) as $day) foreach ($students->take(rand(2,6)) as $i=>$student) {
            $date=now()->subDays($day); $in=$date->copy()->setTime(7+($i%2),rand(0,40));
            Attendance::create(['student_id'=>$student->id,'attendance_date'=>$date->toDateString(),'time_in'=>$in,'time_out'=>$day===0&&$i>2?null:$in->copy()->addHours(8),'status'=>$in->format('H:i')>'08:15'?'late':'present']);
        }
    }
}
