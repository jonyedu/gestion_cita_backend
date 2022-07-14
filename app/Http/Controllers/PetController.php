<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetRequest;
use App\Models\Pet;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($search)
    {
        try {
            $pets = Pet::select('id as pet_id', 'name')->where('name', 'like', "%{$search}%")->where('status', 1)->get();
            return response()->json(['pets' => $pets], 200);
        } catch (Exception $e) {
            return response()->json(['msj' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getItem(Request $request)
    {
        try {
            return  response()->json(['pets' => $this->getItemFilter($request)], 200);
        } catch (Exception $e) {
            return response()->json(['mensaje' => 'PetController - getItem() ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\Seguridad\Pet\Pet
     */
    public function getItemFilter($request)
    {
        $select = $request->select;
        $search = $request->search;
        $options = (object) $request->options;
        //! 1) esto es para capturar el tipo de ordenamiento asc o desc
        $is_desc = false;
        $tipo_ordenamiento = 'asc';
        $name_column = "id";

        $arr = (array)$options;

        if (count($arr) > 0) {

            if (count($options->sortDesc) > 0) {
                $is_desc = $options->sortDesc[0];
            }
            if ($is_desc) {
                $tipo_ordenamiento = 'desc';
            }

            //! 2) esto es para saber la columna a ordenar

            if (count($options->sortBy) > 0) {
                $name_column = $options->sortBy[0];
            }
        } else {
            $options = (object) [];
            $options->itemsPerPage = 5;
        }

        $pets = Pet::select(
            'id as pet_id',
            'name',
            'age',
            'people_id',
            'pet_type_id',
        )
            ->orWhere('name', 'like', '%' . $search . '%')
            ->where(function (Builder $query) use ($select) {
                if (is_null($select)) return;
                $query->where('es_activo', $select);
            })
            ->where('status', true)
            ->people()
            ->petType()
            ->orderBy($name_column, $tipo_ordenamiento)
            ->paginate($options->itemsPerPage);

        return $pets;
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
    public function store(PetRequest $request)
    {
        try {
            $user = Auth::user();

            Pet::create([
                'people_id' => decrypt($request->people_id),
                'pet_type_id' => decrypt($request->pet_type_id),
                'name' => $request->name,
                'age' => $request->age,
                'created_usu' => $user->id,
                'updated_usu' => $user->id,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ]);

            return  response()->json(['msj' => 'Guardado Correctamente'], 201);
        } catch (Exception $e) {
            return response()->json(['mensaje' => 'PetController - store() ' . $e->getMessage()], 500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PetRequest $request, $pet_id)
    {
        try {
            $user = Auth::user();
            $pet = Pet::find(decrypt($pet_id));

            $pet->update([
                'people_id' => decrypt($request->people_id),
                'pet_type_id' => decrypt($request->pet_type_id),
                'name' => $request->name,
                'age' => $request->age,
                'created_usu' => $user->id,
                'updated_usu' => $user->id,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ]);

            return  response()->json(['msj' => 'Modificado Correctamente'], 201);
        } catch (Exception $e) {
            return response()->json(['mensaje' => 'PetController - update() ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $pet_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($pet_id)
    {
        try {
            $user = Auth::user();
            $pet = Pet::find(decrypt($pet_id));

            $pet->update([
                'status' => false,
                'created_usu' => $user->id,
                'updated_usu' => $user->id,
                'updated_ip' => $_SERVER["REMOTE_ADDR"],
            ]);

            return  response()->json(['msj' => 'Elminado Correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['mensaje' => 'PetController - destroy() ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove Massive the specified resource from storage.
     *
     * @param  int  $pet_id
     * @return \Illuminate\Http\Response
     */
    public function destroyMassive(Request $request)
    {
        try {
            $user = Auth::user();
            foreach ($request->selected as $pet) {
                $pet = (object) $pet;
                $pet = Pet::find(decrypt($pet->pet_id));
                $pet->update([
                    'status' => false,
                    'created_usu' => $user->id,
                    'updated_usu' => $user->id,
                    'updated_ip' => $_SERVER["REMOTE_ADDR"],
                ]);
            }
            return  response()->json(['msj' => 'Elminado Correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['mensaje' => 'PetController - destroyMassive() ' . $e->getMessage()], 500);
        }
    }
}
