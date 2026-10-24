<?php
namespace Database\Seeders;

use App\Models\{Exam, FeeInvoice, FeeStructure, SchoolClass, Student, Subject, Timetable, User};
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'admin@school.test')->exists()) return; // already seeded (shared DB)

        User::create(['name' => 'Super Admin', 'email' => 'admin@school.test', 'password' => 'password', 'role' => 'admin']);
        $teacher = User::create(['name' => 'Priya Teacher', 'email' => 'teacher@school.test', 'password' => 'password', 'role' => 'teacher']);

        $c6 = SchoolClass::create(['name' => 'Class 6', 'section' => 'A']);
        SchoolClass::create(['name' => 'Class 7', 'section' => 'A']);

        $subjects = collect(['Tamil', 'English', 'Maths', 'Science', 'Social'])
            ->map(fn ($n) => Subject::create(['name' => $n, 'code' => strtoupper(substr($n, 0, 3))]));

        foreach (range(1, 5) as $day) {
            foreach (range(1, 4) as $p) {
                Timetable::create([
                    'school_class_id' => $c6->id, 'subject_id' => $subjects[($day + $p) % 5]->id, 'teacher_id' => $teacher->id,
                    'day' => $day, 'period' => $p,
                    'start_time' => sprintf('%02d:00', 8 + $p), 'end_time' => sprintf('%02d:45', 8 + $p),
                ]);
            }
        }

        $stu = User::create(['name' => 'Arun Ravi', 'email' => 'student@school.test', 'password' => 'password', 'role' => 'student']);
        $student = Student::create(['user_id' => $stu->id, 'school_class_id' => $c6->id, 'admission_no' => 'ADM001', 'roll_no' => 1, 'gender' => 'Male']);

        $fs = FeeStructure::create(['school_class_id' => $c6->id, 'title' => 'Term 1 Tuition', 'amount' => 15000, 'due_date' => now()->addMonth()]);
        FeeInvoice::create(['student_id' => $student->id, 'fee_structure_id' => $fs->id, 'amount' => 15000, 'due_date' => $fs->due_date]);

        Exam::create(['name' => 'Quarterly Exam', 'school_class_id' => $c6->id, 'start_date' => now()->addWeeks(2)]);
    }
}
