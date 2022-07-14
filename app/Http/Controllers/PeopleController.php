<?php

namespace App\Http\Controllers;

use App\Models\People;
use App\Models\PeopleHasPeopleType;
use Exception;
use Illuminate\Http\Request;

class PeopleController extends Controller
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
            $peoples = People::select('id as people_id', 'name')->where('name', 'like', "%{$search}%")->where('status', 1)->get();
            return response()->json(['peoples' => $peoples], 200);
        } catch (Exception $e) {
            return response()->json(['msj' => $e->getMessage()], 500);
        }
    }

    /**
     * Method to show the people, by means of the type_person_id.
     *
     * @return \Illuminate\Http\Response
     */
    public function getForTypePeople($type_people_id)
    {
        try {
            $peoples_ids = PeopleHasPeopleType::where('type_people_id', $type_people_id)
                ->where('status', true)
                ->where('is_visible', true)
                ->pluck('id');

            $peoples = People::select(
                'id as people_id',
                'name',
                'identification_card',
                'phone',
                'direction',
            )
                ->whereIn('id', $peoples_ids)
                ->paginate(10);


            return response()->json(['peoples' => $peoples]);
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
     * @param  \App\Models\People  $people
     * @return \Illuminate\Http\Response
     */
    public function show(People $people)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\People  $people
     * @return \Illuminate\Http\Response
     */
    public function edit(People $people)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\People  $people
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, People $people)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\People  $people
     * @return \Illuminate\Http\Response
     */
    public function destroy(People $people)
    {
        //
    }
}
