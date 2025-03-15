<?php

namespace Modules\Floramom\Http\Controllers;

use Modules\Floramom\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FloramomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('floramom::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('floramom::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('floramom::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('floramom::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
