<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('school_classes')) Schema::create('school_classes', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('section')->nullable(); $t->timestamps();
        });
        if (!Schema::hasTable('subjects')) Schema::create('subjects', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('code')->nullable()->unique(); $t->timestamps();
        });
        if (!Schema::hasTable('students')) Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('school_class_id')->constrained('school_classes');
            $t->string('admission_no')->unique();
            $t->unsignedInteger('roll_no')->nullable();
            $t->date('dob')->nullable();
            $t->string('gender')->nullable();
            $t->text('address')->nullable();
            $t->timestamps();
        });
        if (!Schema::hasTable('attendances')) Schema::create('attendances', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->foreignId('school_class_id')->constrained('school_classes');
            $t->date('date');
            $t->string('status'); // present | absent | late
            $t->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->unique(['student_id', 'date']);
        });
        if (!Schema::hasTable('timetables')) Schema::create('timetables', function (Blueprint $t) {
            $t->id();
            $t->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $t->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $t->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $t->unsignedTinyInteger('day'); // 1=Mon ... 6=Sat
            $t->unsignedTinyInteger('period');
            $t->time('start_time'); $t->time('end_time');
            $t->timestamps();
        });
        if (!Schema::hasTable('fee_structures')) Schema::create('fee_structures', function (Blueprint $t) {
            $t->id();
            $t->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $t->string('title'); $t->decimal('amount', 10, 2); $t->date('due_date');
            $t->timestamps();
        });
        if (!Schema::hasTable('fee_invoices')) Schema::create('fee_invoices', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->foreignId('fee_structure_id')->constrained('fee_structures')->cascadeOnDelete();
            $t->decimal('amount', 10, 2); $t->date('due_date');
            $t->string('status')->default('unpaid'); // unpaid | partial | paid
            $t->timestamps();
        });
        if (!Schema::hasTable('payments')) Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('fee_invoice_id')->constrained('fee_invoices')->cascadeOnDelete();
            $t->decimal('amount', 10, 2);
            $t->string('method'); $t->date('paid_on'); $t->string('receipt_no')->unique();
            $t->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
        if (!Schema::hasTable('exams')) Schema::create('exams', function (Blueprint $t) {
            $t->id(); $t->string('name');
            $t->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $t->date('start_date')->nullable(); $t->timestamps();
        });
        if (!Schema::hasTable('marks')) Schema::create('marks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $t->decimal('marks_obtained', 5, 2); $t->decimal('max_marks', 5, 2)->default(100);
            $t->timestamps();
            $t->unique(['exam_id', 'student_id', 'subject_id']);
        });
    }
    public function down(): void
    {
        foreach (['marks','exams','payments','fee_invoices','fee_structures','timetables','attendances','students','subjects','school_classes'] as $tb) {
            Schema::dropIfExists($tb);
        }
    }
};
