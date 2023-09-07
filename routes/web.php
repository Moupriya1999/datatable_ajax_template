<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeesController; 

Route::get('/', [EmployeesController::class, 'index']); 
Route::get('/getEmployees', [EmployeesController::class, 'getEmployees'])->name('getEmployees');
Route::get('/getFilteredEmployees', [EmployeesController::class, 'getFilteredEmployees'])->name('getFilteredEmployees');


// Routes for editing and deleting employees
Route::get('/get-employee/{id}', [EmployeesController::class, 'getEmployee'])->name('getEmployee');
Route::put('/edit-employee/{id}', [EmployeesController::class, 'updateEmployee'])->name('updateEmployee');
Route::delete('/delete-employee/{id}', [EmployeesController::class, 'deleteEmployee'])->name('deleteEmployee');

Route::delete('/delete-selected-employees', [EmployeesController::class, 'deleteSelectedEmployees'])->name('deleteSelectedEmployees');


// Route::get('/', function () {
//     return view('welcome');
// });
