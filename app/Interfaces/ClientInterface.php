<?php


namespace App\Interfaces;


use Illuminate\Http\Request;

interface ClientInterface
{
    public function index();
    public function importCsv(Request $request);
}
