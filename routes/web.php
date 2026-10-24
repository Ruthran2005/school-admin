<?php
use App\Http\Controllers\{AdminUserController, AttendanceController, AuthController, DashboardController, ExamController, FeeStructureController,
    InvoiceController, MarksController, ReportController, SchoolClassController, StudentController, SubjectController,
    TeacherController, TimetableController};
use App\Http\Middleware\RoleMiddleware as Role;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware(['auth', Role::class . ':admin'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('password', [AuthController::class, 'showPassword'])->name('password.edit');
    Route::put('password', [AuthController::class, 'updatePassword'])->name('password.update');
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('report/{student}/{exam}', [ReportController::class, 'card'])->middleware('module:exams')->name('report.card');
    Route::get('receipts/{payment}', [InvoiceController::class, 'receipt'])->middleware('module:invoices')->name('receipts.show');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('classes', SchoolClassController::class)->except('show')->middleware('module:classes');
        Route::resource('subjects', SubjectController::class)->except('show')->middleware('module:subjects');
        Route::resource('teachers', TeacherController::class)->except('show')->middleware('module:teachers');
        Route::resource('students', StudentController::class)->except('show')->middleware('module:students');
        Route::resource('timetable', TimetableController::class)->except('show')->middleware('module:timetable');
        Route::resource('fee-structures', FeeStructureController::class)->except('show')->middleware('module:fees');
        Route::get('exams/{exam}/results', [ExamController::class, 'results'])->middleware('module:exams')->name('exams.results');
        Route::resource('exams', ExamController::class)->except('show')->middleware('module:exams');
        Route::resource('admins', AdminUserController::class)->except('show')->middleware('module:admins');
        Route::get('invoices', [InvoiceController::class, 'index'])->middleware('module:invoices')->name('invoices.index');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->middleware('module:invoices')->name('invoices.show');
        Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'pay'])->middleware('module:invoices')->name('invoices.pay');
    });

    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('attendance', [AttendanceController::class, 'index'])->middleware('module:attendance')->name('attendance');
        Route::post('attendance', [AttendanceController::class, 'store'])->middleware('module:attendance')->name('attendance.store');
        Route::get('marks', [MarksController::class, 'index'])->middleware('module:marks')->name('marks');
        Route::post('marks', [MarksController::class, 'store'])->middleware('module:marks')->name('marks.store');
    });
});
