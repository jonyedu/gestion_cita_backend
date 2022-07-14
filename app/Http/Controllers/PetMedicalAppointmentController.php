<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetMedicalAppointmentRequest;
use App\Models\PetMedicalAppointment;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetMedicalAppointmentController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getItem(Request $request)
    {
        try {
            return  response()->json(['pet_medical_appointments' => $this->getItemFilter($request)], 200);
        } catch (Exception $e) {
            return response()->json(['mensaje' => 'PetMedicalAppointmentController - getItem() ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\Seguridad\PetMedicalAppointment\PetMedicalAppointment
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

        $pet_medical_appointments = PetMedicalAppointment::select(
            'id as pet_medical_appointment_id',
            'pet_id',
            'registration_date',
            'registration_time',
            'turn',
        )
            ->orWhere('turn', 'like', '%' . $search . '%')
            ->where(function (Builder $query) use ($select) {
                if (is_null($select)) return;
                $query->where('es_activo', $select);
            })
            ->pet()
            ->where('status', true)
            ->orderBy($name_column, $tipo_ordenamiento)
            ->paginate($options->itemsPerPage);

        return $pet_medical_appointments;
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
    public function store(PetMedicalAppointmentRequest $request)
    {
        try {
            $user = Auth::user();

            PetMedicalAppointment::create([
                'pet_id' => decrypt($request->pet_id),
                'registration_date' => $request->registration_date,
                'registration_time' => $request->registration_time,
                'turn' => $request->turn,
                'created_usu' => $user->id,
                'updated_usu' => $user->id,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ]);

            return  response()->json(['msj' => 'Guardado Correctamente'], 201);
        } catch (Exception $e) {
            return response()->json(['mensaje' => 'PetMedicalAppointmentController - store() ' . $e->getMessage()], 500);
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
    public function update(PetMedicalAppointmentRequest $request, $pet_medical_appointment_id)
    {
        try {
            $user = Auth::user();
            $pet = PetMedicalAppointment::find(decrypt($pet_medical_appointment_id));

            $pet->update([
                'pet_id' => decrypt($request->pet_id),
                'registration_date' => $request->registration_date,
                'registration_time' => $request->registration_time,
                'turn' => $request->turn,
                'created_usu' => $user->id,
                'updated_usu' => $user->id,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ]);

            return  response()->json(['msj' => 'Modificado Correctamente'], 201);
        } catch (Exception $e) {
            return response()->json(['mensaje' => 'PetMedicalAppointmentController - update() ' . $e->getMessage()], 500);
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
            $pet = PetMedicalAppointment::find(decrypt($pet_id));

            $pet->update([
                'status' => false,
                'created_usu' => $user->id,
                'updated_usu' => $user->id,
                'updated_ip' => $_SERVER["REMOTE_ADDR"],
            ]);

            return  response()->json(['msj' => 'Elminado Correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['mensaje' => 'PetMedicalAppointmentController - destroy() ' . $e->getMessage()], 500);
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
                $pet = PetMedicalAppointment::find(decrypt($pet->pet_id));
                $pet->update([
                    'status' => false,
                    'created_usu' => $user->id,
                    'updated_usu' => $user->id,
                    'updated_ip' => $_SERVER["REMOTE_ADDR"],
                ]);
            }
            return  response()->json(['msj' => 'Elminado Correctamente'], 200);
        } catch (Exception $e) {
            return response()->json(['mensaje' => 'PetMedicalAppointmentController - destroyMassive() ' . $e->getMessage()], 500);
        }
    }
}
