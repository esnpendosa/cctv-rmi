<?php

use App\Models\Camera;
use App\Http\Resources\CameraResource;
use Illuminate\Support\Facades\Route;

Route::get('/cameras/status', function () {
    return CameraResource::collection(Camera::with('location')->get());
});
