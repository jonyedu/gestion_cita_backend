<?php

namespace App\Http\Controllers;

use App\Models\PetType;
use Exception;
use Illuminate\Http\Request;

class PetTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($search)
    {
        try {
            $pets_types = PetType::select('id as pet_type_id', 'descripcion')->where('descripcion', 'like', "%{$search}%")->where('status', 1)->get();
            return response()->json(['pets_types' => $pets_types], 200);
        } catch (Exception $e) {
            return response()->json(['msj' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PetType  $petType
     * @return \Illuminate\Http\Response
     */
    public function show(PetType $petType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PetType  $petType
     * @return \Illuminate\Http\Response
     */
    public function edit(PetType $petType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PetType  $petType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PetType $petType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PetType  $petType
     * @return \Illuminate\Http\Response
     */
    public function destroy(PetType $petType)
    {
        //
    }
}
