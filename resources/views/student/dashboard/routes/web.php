<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\BioNoteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\ExamController as StudentExam;
use App\Http\Controllers\Student\ProfileController as StudentProfile;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ────────────────────────────────────────────────────────────

Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default   => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Admin Routes ─────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:admin', 'account.active', 'no-cache'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Students
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::patch('/students/{student}/toggle-active', [StudentController::class, 'toggleActive'])->name('students.toggle-active');
    Route::post('/students/{student}/reset-password', [StudentController::class, 'resetPassword'])->name('students.reset-password');

    // Exams
    Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');
    Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
    Route::get('/exams/{exam}', [ExamController::class, 'show'])->name('exams.show');
    Route::get('/exams/{exam}/edit', [ExamController::class, 'edit'])->name('exams.edit');
    Route::put('/exams/{exam}', [ExamController::class, 'update'])->name('exams.update');
    Route::delete('/exams/{exam}', [ExamController::class, 'destroy'])->name('exams.destroy');
    Route::patch('/exams/{exam}/toggle-active', [ExamController::class, 'toggleActive'])->name('exams.toggle-active');
    Route::post('/exams/{exam}/duplicate', [ExamController::class, 'duplicate'])->name('exams.duplicate');
    Route::post('/exams/{exam}/regenerate-code', [ExamController::class, 'regenerateCode'])->name('exams.regenerate-code');
    Route::put('/exams/{exam}/interpretations', [ExamController::class, 'updateInterpretations'])->name('exams.interpretations.update');

    // Questions
    Route::get('/exams/{exam}/questions', [QuestionController::class, 'index'])->name('questions.index');
    Route::post('/exams/{exam}/sections', [QuestionController::class, 'storeSection'])->name('sections.store');
    Route::post('/exams/{exam}/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::put('/exams/{exam}/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/exams/{exam}/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
    Route::post('/exams/{exam}/questions/reorder', [QuestionController::class, 'reorder'])->name('questions.reorder');

    // Monitoring
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/live', [MonitoringController::class, 'live'])->name('monitoring.live');
    Route::post('/monitoring/{session}/invalidate', [MonitoringController::class, 'invalidate'])->name('monitoring.invalidate');

    // Results
    Route::get('/results', [ResultController::class, 'index'])->name('results.index');
    Route::get('/results/{session}', [ResultController::class, 'show'])->name('results.show');
    Route::post('/results/{session}/grade', [ResultController::class, 'grade'])->name('results.grade');
    Route::get('/results/{session}/admission-slip', [ResultController::class, 'admissionSlip'])->name('results.admission-slip');

    // Bio Notes
    Route::post('/profiles/{profile}/bio-notes', [BioNoteController::class, 'store'])->name('bio-notes.store');
    Route::put('/bio-notes/{bioNote}', [BioNoteController::class, 'update'])->name('bio-notes.update');
    Route::delete('/bio-notes/{bioNote}', [BioNoteController::class, 'destroy'])->name('bio-notes.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');

    // Backup
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup', [BackupController::class, 'create'])->name('backup.create');
    Route::get('/backup/{filename}/download', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backup/{filename}', [BackupController::class, 'destroy'])->name('backup.destroy');
});

// ─── Student Routes ───────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:student', 'account.active', 'no-cache'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
    Route::get('/profile', [StudentProfile::class, 'index'])->name('profile');

    // Exam
    Route::get('/exam/enter-code', [StudentExam::class, 'enterCode'])->name('exam.enter-code');
    Route::post('/exam/start', [StudentExam::class, 'start'])->name('exam.start');
    Route::get('/exam/{session}/take', [StudentExam::class, 'take'])->name('exam.take');
    Route::post('/exam/{session}/auto-save', [StudentExam::class, 'autoSave'])->name('exam.auto-save');
    Route::get('/exam/{session}/sync-timer', [StudentExam::class, 'syncTimer'])->name('exam.sync-timer');
    Route::post('/exam/{session}/focus-lost', [StudentExam::class, 'focusLost'])->name('exam.focus-lost');
    Route::post('/exam/{session}/submit', [StudentExam::class, 'submit'])->name('exam.submit');
    Route::get('/exam/{session}/result', [StudentExam::class, 'result'])->name('exam.result');
});
