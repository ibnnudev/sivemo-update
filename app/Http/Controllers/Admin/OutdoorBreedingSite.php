<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OutdoorBreedingSite extends Controller
{
    function artificial_index() {
        return view('admin.outdoor-breeding.artificial.index');
    }
    function natural_index() {
        return view('admin.outdoor-breeding.natural.index');
    }
}
