<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataMahasiswa;
use Psy\CodeCleaner\ValidConstructorPass;

class PagesController extends Controller
{
    public function home(){
        return view('index');
    }

    public function about(){
        return view('about');
    }
}


