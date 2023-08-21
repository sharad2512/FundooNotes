<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/user/register', [UserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/user/reset-password', [UserController::class, 'resetPassword']);

//group middleware 
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/userDetails', [UserController::class, 'userDetails']);
    Route::post('/user/logout', [UserController::class, 'logout']);

    Route::post('/note/create', [NoteController::class, 'create']);
    Route::get('/note/get', [NoteController::class, 'getNotes']);
    Route::put('/note/edit/{id}', [NoteController::class, 'editNote']);
    Route::delete('/note/delete/{id}',[NoteController::class,'deleteNote']);
    Route::put('/note/archived/{id}',[NoteController::class,'is_archived']);
    Route::put('/note/pinned/{id}',[NoteController::class,'pinnedNote']);

    Route::post('/label/create',[LabelController::class,'createLabel']);
    Route::post('/addNoteTolabels/{labelsId}/notes/{noteId}',[LabelController::class,'addNoteToLabel']);
    Route::delete('/deleteNoteFromLabel/{labelsId}/notes/{noteId}',[LabelController::class,'deleteNoteFromLabel']);
    Route::delete('/label/delete/{id}',[LabelController::class,'deleteLabel']);
    Route::put('/label/update/{id}',[LabelController::class,'updateLabel']);
});
