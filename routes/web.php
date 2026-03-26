<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function (Illuminate\Http\Request $request) {
    if ($request->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($request->user()->role === 'gerente') {
        return redirect()->route('gerente.dashboard');
    }
    return redirect()->route('empleado.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');

    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);

    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->names('admin.categories');
    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class)->names('admin.courses');
    Route::resource('areas', \App\Http\Controllers\Admin\AreaController::class)->names('admin.areas');
    
    // Lessons Nested Routes
    Route::get('courses/{course}/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'index'])->name('admin.courses.lessons.index');
    Route::post('courses/{course}/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'store'])->name('admin.courses.lessons.store');
    Route::get('lessons/{lesson}/edit', [\App\Http\Controllers\Admin\LessonController::class, 'edit'])->name('admin.lessons.edit');
    Route::put('lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'update'])->name('admin.lessons.update');
    Route::delete('lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'destroy'])->name('admin.lessons.destroy');

    // Evaluations
    Route::get('courses/{course}/evaluations', [\App\Http\Controllers\Admin\EvaluationController::class, 'index'])->name('admin.courses.evaluations.index');
    Route::post('courses/{course}/evaluations', [\App\Http\Controllers\Admin\EvaluationController::class, 'store'])->name('admin.courses.evaluations.store');
    Route::delete('evaluations/{evaluation}', [\App\Http\Controllers\Admin\EvaluationController::class, 'destroy'])->name('admin.evaluations.destroy');
});

// Gerente Routes
Route::middleware(['auth', 'role:gerente'])->prefix('gerente')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\GerenteController::class, 'dashboard'])->name('gerente.dashboard');
});

// Empleado Routes
Route::middleware(['auth', 'role:empleado'])->prefix('empleado')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\EmpleadoController::class, 'dashboard'])->name('empleado.dashboard');
    Route::get('/courses/{course}', [\App\Http\Controllers\EmpleadoController::class, 'showCourse'])->name('empleado.courses.show');
    Route::get('/courses/{course}/lessons/{lesson}', [\App\Http\Controllers\EmpleadoController::class, 'showLesson'])->name('empleado.lessons.show');
    Route::post('/lessons/{lesson}/complete', [\App\Http\Controllers\EmpleadoController::class, 'completeLesson'])->name('empleado.lessons.complete');

    // Quiz taking
    Route::get('/quizzes/{evaluation}', [\App\Http\Controllers\QuizController::class, 'show'])->name('empleado.quizzes.show');
    Route::post('/quizzes/{evaluation}/submit', [\App\Http\Controllers\QuizController::class, 'submit'])->name('empleado.quizzes.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
