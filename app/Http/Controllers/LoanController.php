<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return 'LoanController@index';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return 'LoanController@create';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return 'LoanController@store';
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return "LoanController@show, id: {$id}";
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return "LoanController@edit, id: {$id}";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return "LoanController@update, id: {$id}";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return "LoanController@destroy, id: {$id}";
    }

    public function kembalikan(string $id)
    {
        return "LoanController@kembalikan, id: {$id}";
    }   

}


