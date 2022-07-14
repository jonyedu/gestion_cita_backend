<?php

use App\Helpers\ValidadorEc;
use App\Models\Admision\Emergencia\RegistroAdmision;
use App\Models\Admision\Paciente\Paciente;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

/**
 * Metodo para devolver la respuesta del validor ECU
 * @param  String  $cedula
 * @return bool
 */
if (!function_exists('getResponseValidadorEc')) {
    function getResponseValidadorEc($is_success = true, $status = 200, $msj = 'Formato Correcto')
    {
        $response = (object) [
            'is_success' => $is_success,
            'status' => $status,
            'msj' => $msj,
        ];
        return $response;
    }
}

/**
 * Metodo para validar CI ECU
 * @param  String  $cedula
 * @return bool
 */
if (!function_exists('splitFullNameToIndividual')) {
    function splitFullNameToIndividual($full_name, $name_padre = '', $name_madre = '')
    {
        // $full_name = 'ALBAN YEPEZ MARIA DE LAS MERCEDES';

        // $name_padre = 'ALMEIDA MORAN KLEBER JAVIER';
        // $name_madre = 'MARQUEZ DE LA PLATA CRESPO MARIA JUANA';
        /* Convierto a mayascula */
        $full_name = Str::upper($full_name);

        /* separar el nombre completo en espacios */
        $tokens = Str::of($full_name)->trim()->explode(' ');
        /* arreglo donde se guardan las "palabras" del nombre */
        $names = array();
        /* palabras de apellidos (y nombres) compuetos */
        $special_tokens = array('DA', 'DE', 'DEL', 'LA', 'LAS', 'LOS', 'MAC', 'MC', 'VAN', 'VON', 'Y', 'I', 'SAN', 'SANTA');

        $prev = "";
        foreach ($tokens as $token) {
            $_token = $token;
            if (in_array($_token, $special_tokens)) {
                $prev .= "$token ";
            } else {
                $names[] = $prev . $token;
                $prev = "";
            }
        }
        $apellido_paterno = '';
        $apellido_materno = '';
        $primer_nombre = '';
        $segundo_nombre = '';
        $validar_name = true;

        $num_nombres = count($names);

        switch ($num_nombres) {
            case 0:
                break;
            case 1:
                $apellido_paterno = $names[0];
                break;
            case 2:
                $apellido_paterno = $names[0];
                $primer_nombre = $names[1];
                break;
            case 3:
                $apellido_paterno = $names[0];
                $apellido_materno = $names[1];
                $primer_nombre = $names[2];
                break;
            case 4:
                $apellido_paterno = $names[0];
                $apellido_materno = $names[1];
                $primer_nombre = $names[2];
                $segundo_nombre = $names[3];
                $validar_name = false;
                break;
            default:

                //name
                $names_paciente = $tokens;

                //name padre
                $names_padre = Str::of($name_padre)->trim()->explode(' ')->toArray();

                //name madre
                $names_madre = Str::of($name_madre)->trim()->explode(' ')->toArray();


                foreach ($names_paciente as $name) {
                    if (in_array($name, $names_padre)) {
                        $apellido_paterno .= "$name ";
                    }
                    if (in_array($name, $names_madre)) {
                        $apellido_materno .= "$name ";
                    }
                }

                $apellido_paterno = Str::of($apellido_paterno)->rtrim();
                $apellido_materno = Str::of($apellido_materno)->rtrim();
                $primer_nombre = $names[3];

                unset($names[0], $names[1], $names[2], $names[3]);
                $segundo_nombre = implode(' ', $names);
                break;
        }

        return  [
            'apellido_paterno' => $apellido_paterno,
            'apellido_materno' => $apellido_materno,
            'primer_nombre' => $primer_nombre,
            'segundo_nombre' => $segundo_nombre,
            'validar_name' => $validar_name,
        ];
    }
}

/**
 * Metodo para validar CI ECU
 * @param  String  $cedula
 * @return bool
 */
if (!function_exists('validarCedulaEc')) {
    function validarCedulaEc($cedula)
    {
        $validador = new ValidadorEc;
        if ($validador->validarCedula($cedula)) return getResponseValidadorEc();

        return getResponseValidadorEc(false, 500, $validador->getError());
    }
}

/**
 * Metodo para calcular la edad del paciente, mediente el paciente_id
 * @param  String  $cedula
 * @return bool
 */
if (!function_exists('calculaEdadFromPacienteId')) {
    function calculaEdadFromPacienteId($paciente_id)
    {
        //return calculaEdadAdmision($this->CirProHisCli);
        //!1) Obtengo la fecha de nacimiento del paciente
        $paciente = Paciente::select('id', 'fecha_nacimiento')->where('status', 1)->where('id', $paciente_id)->first();

        // DB::connection('control_hospitalario_db_sql')
        // ->select('select * from tbPaciente where id = 206178');

        //Paciente::select('id', 'fecha_nacimiento')->where('status', 1)->where('id', $paciente_id)->get();
        //!2) Obtengo la fecha de registro en admision
        $registro_admision = RegistroAdmision::select('paciente', 'fecha_ingreso')->where('paciente', $paciente_id)->orderBy('fecha_ingreso', 'desc')->first();
        //!3 Valido que ninguno venga null
        if ($paciente == null || $registro_admision == null) {
            $fecha_nacimiento = $paciente != null ? $paciente->fecha_nacimiento : date("Y-m-d H:i:s");
            $fecha_actual = date("Y-m-d H:i:s");
            //!5 creo objetos de fecha
            $fecha_nac = new DateTime(date('Y/m/d', strtotime($fecha_nacimiento))); // Creo un objeto DateTime de la fecha ingresada
            $fecha_hoy =  new DateTime(date('Y/m/d', strtotime($fecha_actual))); // Creo un objeto DateTime de la fecha de hoy
            //!6 Calculo la diferencia entre fechas
            $edad = date_diff($fecha_hoy, $fecha_nac); // La funcion ayuda a calcular la diferencia, esto seria un objeto
            //!7 Retorna año y mes
            return $edad->format('%y') . ' años ' . $edad->format('%m') . ' meses ';
        } else {
            $paciente->makeHidden(['EDAD']);
            $fecha_nacimiento = $paciente->fecha_nacimiento;
            $fecha_ingreso_admision = $registro_admision->fecha_ingreso;
            //!5 creo objetos de fecha
            $fecha_nac = new DateTime(date('Y/m/d', strtotime($fecha_nacimiento))); // Creo un objeto DateTime de la fecha ingresada
            $fecha_hoy =  new DateTime(date('Y/m/d', strtotime($fecha_ingreso_admision))); // Creo un objeto DateTime de la fecha de hoy
            //!6 Calculo la diferencia entre fechas
            $edad = date_diff($fecha_hoy, $fecha_nac); // La funcion ayuda a calcular la diferencia, esto seria un objeto
            //!7 Retorna año y mes
            return $edad->format('%y') . ' años ' . $edad->format('%m') . ' meses ';
        }
        //!4 Setteo los valores

    }
}

/**
 * Metodo para calcular la edad del paciente, mediente el paciente_id
 * @param  String  $cedula
 * @param  bool  $year, si es true, devolverá solo el año
 * @return bool
 */
if (!function_exists('calculaEdadFromFecha')) {
    function calculaEdadFromFecha($fecha_nacimiento, $year = false)
    {
        $fecha_actual = date("Y-m-d H:i:s");
        //!5 creo objetos de fecha
        $fecha_nac = new DateTime(date('Y/m/d', strtotime($fecha_nacimiento))); // Creo un objeto DateTime de la fecha ingresada
        $fecha_hoy =  new DateTime(date('Y/m/d', strtotime($fecha_actual))); // Creo un objeto DateTime de la fecha de hoy
        //!6 Calculo la diferencia entre fechas
        $edad = date_diff($fecha_hoy, $fecha_nac); // La funcion ayuda a calcular la diferencia, esto seria un objeto
        //!7 Retorna año y mes
        if ($year) return intval($edad->format('%y'));
        return "{$edad->format('%y')} años {$edad->format('%m')} meses";
    }
}

/**
 * Metodo para calcular la edad del paciente, mediente el paciente_id
 * @param  String  $cedula
 * @param  bool  $year, si es true, devolverá solo el año
 * @return bool
 */
if (!function_exists('url_exists')) {
    function url_exists($url = NULL)
    {

        if (empty($url)) {
            return false;
        }

        $options['http'] = array(
            'method' => "HEAD",
            'ignore_errors' => 1,
            'max_redirects' => 0
        );
        $body = @file_get_contents($url, false, stream_context_create($options));

        // Ver http://php.net/manual/es/reserved.variables.httpresponseheader.php
        if (isset($http_response_header)) {
            sscanf($http_response_header[0], 'HTTP/%*d.%*d %d', $httpcode);

            // Aceptar solo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
            $accepted_response = array(200, 301, 302);
            if (in_array($httpcode, $accepted_response)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

/**
 * Metodo para validar RUC persona natural
 * @param  String  $cedula
 * @return bool
 */
if (!function_exists('validarRucPersonaNaturalEc')) {
    function validarRucPersonaNaturalEc($cedula)
    {
        $validador = new ValidadorEc;
        if ($validador->validarRucPersonaNatural($cedula)) return getResponseValidadorEc();

        return getResponseValidadorEc(false, 500, $validador->getError());
    }
}

/**
 * Metodo para validar RUC sociedad privada
 * @param  String  $cedula
 * @return bool
 */
if (!function_exists('validarRucSociedadPrivadaEc')) {
    function validarRucSociedadPrivadaEc($cedula)
    {
        $validador = new ValidadorEc;
        if ($validador->validarRucSociedadPrivada($cedula)) return getResponseValidadorEc();

        return getResponseValidadorEc(false, 500, $validador->getError());
    }
}

/**
 * Metodo para validar RUC sociedad pública
 * @param  String  $cedula
 * @return bool
 */
if (!function_exists('validarRucSociedadPublicaEc')) {
    function validarRucSociedadPublicaEc($cedula)
    {
        $validador = new ValidadorEc;
        if ($validador->validarRucSociedadPublica($cedula)) return getResponseValidadorEc();

        return getResponseValidadorEc(false, 500, $validador->getError());
    }
}


/**
 * Method to convert file to binary.
 * @param  String  $data
 * @return base64_encode
 */
if (!function_exists('convertImgToBase64')) {
    function convertImgToBase64($data)
    {
        // Extensión de la imagen
        $type = pathinfo($data, PATHINFO_EXTENSION);

        // Cargando la imagen
        $data = file_get_contents($data);

        // Decodificando la imagen en base64
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}

/**
 * Method to convert base64 to binary.
 * @param  String  $data
 * @return binary
 */
if (!function_exists('convertBase64ToBinary')) {
    function convertBase64ToBinary($data)
    {
        $img  = file_get_contents($data);
        $binary = DB::raw('CONVERT(VARBINARY(MAX), 0x' . bin2hex($img) . ')');
        return $binary;
    }
}

/**
 * Method to calcular la diferencia entre hora
 * @param  Date  $hora_inicio
 * @param  Date  $hora_fin
 * @return Integer $total
 */
if (!function_exists('calcularHoraInicioFin')) {
    function calcularHoraInicioFin($hora_inicio, $hora_fin)
    {
        //!1 Convierto el time a un objeto de DateTime()
        $inicio = new DateTime($hora_inicio); // Creo un objeto DateTime de la hora inicio
        $fin =  new DateTime($hora_fin); // Creo un objeto DateTime de la hora de fin
        //!2 Calculo la diferencia entre las horas
        $edad = $inicio->diff($fin); // La funcion ayuda a calcular la diferencia, esto seria un objeto
        //!3 Concateno las horas
        $total = $edad->format('%h') . ':' . $edad->format('%i') . ':' . $edad->format('%s');
        //!4 Retorno los valores
        return $total;
    }
}

/**
 * Method to generate un PDF
 * @param  String  $url_blade
 * @param  Object  $data
 * @param  String  $nombreArchivo
 * @param  String  $setPaper
 * @return \Barryvdh\DomPDF $pdf
 */
if (!function_exists('generateReportPdf')) {
    function generateReportPdf($url_blade, $data, $nombreArchivo, $setPaper = 'A4', $orientation = '', bool $is_save = false)
    {
        $pdf = PDF::loadView($url_blade, $data)
            ->setPaper($setPaper, $orientation);
        return !$is_save ? $pdf->stream($nombreArchivo) : $pdf->output();
    }
}

/**
 * Method to generate un Excel
 * @param  Class  $class
 * @param  String  $nombreArchivo
 * @return Maatwebsite\Excel\Facades $excel
 */
if (!function_exists('generateReportExcel')) {
    function generateReportExcel($class, $nombreArchivo)
    {
        return Excel::download($class, $nombreArchivo);
    }
}

/**
 * Method to import un Excel
 * @param  Class  $class
 * @param  File  $file
 * @return Boolean
 */
if (!function_exists('importExcel')) {
    function importExcel($class, $file)
    {
        Excel::import($class, $file);
        return 'Se importo correctamente';
    }
}

/**
 * Method to validate rango fecha
 * @param  Date  $fecha_desde
 * @param  Date  $fecha_hasta
 * @param  Intenger  $valor_comparar
 * @return Boolean
 */
if (!function_exists('validarRangoFechaBusqueda')) {
    function validarRangoFechaBusqueda($fecha_desde, $fecha_hasta, $valor_comparar = 1)
    {
        $date1 = new DateTime($fecha_desde);
        $date2 = new DateTime($fecha_hasta);

        $diff = $date1->diff($date2);

        if ($diff->m > 1) throw new Exception("Solo es permitido " . $valor_comparar . " mes de busqueda, por favor cambie los parametros de fecha.");
    }
}

/**
 * Method to get the ID paciente mendiante la busqueda de los nombres
 * @param  Illuminate\Http\Request  $request
 * @return Array
 */
if (!function_exists('getIdPacientePorSearch')) {
    function getIdPacientePorSearch($request)
    {
        $pacientes_id = [];
        //se divide el string, en array para buscar por separados
        $full_name_paciente = explode(" ", $request->paciente);

        //Se setea los valores
        $apellido_paterno = count($full_name_paciente) > 0 ? $full_name_paciente[0] : "";
        $apellido_materno = count($full_name_paciente) > 1 ? $full_name_paciente[1] : "";
        $primer_nombre = count($full_name_paciente) > 2 ? $full_name_paciente[2] : "";
        $segundo_nombre  = count($full_name_paciente) > 3 ? $full_name_paciente[3] : "";

        //Se procede a buscar
        $pacientes = Paciente::select('id')
            ->limit(50)
            //esto se descomenta cuando cambien el servidor sql en produccion
            //->whereRaw("concat(apellido_paterno,' ',apellido_materno,' ',primer_nombre,' ',segundo_nombre) = ?", [$request->paciente])
            ->where('apellido_paterno', 'like', "%{$apellido_paterno}%")
            ->where('apellido_materno', 'like', "%{$apellido_materno}%")
            ->where('primer_nombre', 'like', "%{$primer_nombre}%")
            ->where('segundo_nombre', 'like', "%{$segundo_nombre}%")
            ->where('cedula', 'like', "%{$request->cedula}%")
            ->where('status', 1)
            ->orderby('apellido_paterno', 'asc')
            ->get();

        //se agrega el id del paciente en un arreglo
        foreach ($pacientes as $key => $paciente) {
            array_push($pacientes_id, $paciente->id);
        }

        return $pacientes_id;
    }
}

/**
 * Method to get the ID paciente mendiante la busqueda de los nombres
 * @param  Illuminate\Http\Request  $request
 * @return Array
 */
if (!function_exists('getFistMonthsYear')) {
    function getFistMonthsYear(bool $getCurrent)
    {
        $format = 'm-d-Y';
        if ($getCurrent) {
            $date_past = Carbon::now()->startOfYear();
        } else {
            $date_past = Carbon::now()->subYear()->startOfYear();
        }


        $months = [
            $date_past->addMonths(0)->format($format),
            $date_past->addMonths(1)->format($format),
            $date_past->addMonths(1)->format($format),
            $date_past->addMonths(1)->format($format),
            $date_past->addMonths(1)->format($format),
            $date_past->addMonths(1)->format($format),
            $date_past->addMonths(1)->format($format),
            $date_past->addMonths(1)->format($format),
            $date_past->addMonths(1)->format($format),
            $date_past->addMonths(1)->format($format),
            $date_past->addMonths(1)->format($format),
            $date_past->addMonths(1)->format($format),
        ];
        return $months;
    }
}

/**
 * Method to get the ID paciente mendiante la busqueda de los nombres
 * @param  Illuminate\Http\Request  $request
 * @return Array
 */
if (!function_exists('getLatesMonthsYear')) {
    function getLatesMonthsYear(bool $getCurrent)
    {
        $format = 'm-d-Y';
        if ($getCurrent) {
            $date_past = Carbon::now()->startOfYear();
        } else {
            $date_past = Carbon::now()->subYear()->startOfYear();
        }

        $months = [
            $date_past->addMonths(0)->endOfMonth()->format($format),
            $date_past->addMonths(1)->subMonths(1)->endOfMonth()->format($format),
            $date_past->addMonths(1)->endOfMonth()->format($format),
            $date_past->addMonths(1)->subMonths(1)->endOfMonth()->format($format),
            $date_past->addMonths(1)->endOfMonth()->format($format),
            $date_past->addMonths(1)->subMonths(1)->endOfMonth()->format($format),
            $date_past->addMonths(1)->endOfMonth()->format($format),
            $date_past->addMonths(1)->endOfMonth()->format($format),
            $date_past->addMonths(1)->subMonths(1)->endOfMonth()->format($format),
            $date_past->addMonths(1)->endOfMonth()->format($format),
            $date_past->addMonths(1)->subMonths(1)->endOfMonth()->format($format),
            $date_past->addMonths(1)->endOfMonth()->format($format),
        ];
        return $months;
    }
}

/**
 * Method to get the ID paciente mendiante la busqueda de los nombres
 * @param  Illuminate\Http\Request  $request
 * @return Array
 */
if (!function_exists('getYearFromTo')) {
    function getYearFromTo()
    {
        $format = 'm-d-Y';
        return (object) [
            'date_first_day_year_past' => Carbon::now()->subYear()->startOfYear()->format($format),
            'date_latest_day_year_past' => Carbon::now()->subYear()->endOfYear()->format($format),
            'date_first_day_2_year_past' => Carbon::now()->subYears(2)->startOfYear()->format($format),
            'date_latest_day_2_year_past' => Carbon::now()->subYears(2)->endOfYear()->format($format),
        ];
    }
}

/**
 * Method to get the ID paciente mendiante la busqueda de los nombres
 * @param  Illuminate\Http\Request  $request
 * @return Array
 */
if (!function_exists('toAbbreviationFirstWordConcatenatesSecond')) {
    function toAbbreviationFirstWordConcatenatesSecond($palabra)
    {
        $descripcion = '';
        if ($palabra != null || $palabra != '') {
            //Obtengo la posicion de la 2 palabra, para luego concatenar con la abreviatura
            $posicion_palabra_obtener = mb_strpos($palabra, ' ');
            //valido que la posicion se haya obtenido
            $texto_area_dividido = explode(" ", $palabra);
            $no_palabra = count($texto_area_dividido);
            if ($no_palabra == 1) return $palabra;

            //si la pabara es 2
            if ($no_palabra == 2) {
                //Obtengo la abrebiatura de la primera palabra
                $abreviatura_1re_palabra = substr($palabra, 0, 1);
                //obtengo la segunda palabra
                $palabra_obtenida = mb_strcut($palabra, $posicion_palabra_obtener + 1);
                //concateno las palabras
                $descripcion = "{$abreviatura_1re_palabra}. {$palabra_obtenida}";
            } else if ($no_palabra == 3) {
                //Obtengo la posicion de la 2 palabra, para luego concatenar con la abreviatura
                $posicion_2da_palabra_obtener = mb_strrpos($palabra, ' ');
                //Obtengo la abreviatura de la primera palabra
                $abreviatura_1re_palabra = mb_strcut($palabra, 0, 1);
                //Obtengo la abrebiatura de la segunda palabra
                $abreviatura_2da_palabra = substr($palabra, $posicion_palabra_obtener + 1, 1);
                //obtengo la tercera palabra
                $palabra_obtenida = mb_strcut($palabra, $posicion_2da_palabra_obtener + 1);
                //concateno las palabras
                $descripcion = "{$abreviatura_1re_palabra}. {$abreviatura_2da_palabra}. {$palabra_obtenida}";
            }
            return $descripcion;
        }
        return $descripcion;
    }
}

/**
 * Method to get the ID paciente mendiante la busqueda de los nombres
 * @param  Illuminate\Http\Request  $request
 * @return Array
 */
if (!function_exists('calcularDiferenciaHoraInicioActual')) {
    function calcularDiferenciaHoraInicioActual($hora_inicio, $estado_cama)
    {
        $estado_cama_id = intval(decrypt($estado_cama));

        //!1 Convierto el time a un objeto de DateTime()
        $inicio = new DateTime($hora_inicio); // Creo un objeto DateTime de la hora inicio
        $actual =  new DateTime(date('Y-m-d H:i:s')); // Creo un objeto DateTime de la hora de fin
        //!2 Calculo la diferencia entre las horas
        $diferencia = $inicio->diff($actual); // La funcion ayuda a calcular la diferencia, esto seria un objeto

        $tiempo_transcurrido = 0;

        //Ocupada
        if ($estado_cama_id == 2) {
            $tiempo_transcurrido =  $diferencia->format('%d') . ' días ' . $diferencia->format('%h') . ' horas ' . $diferencia->format('%i') . ' minutos ';
        }
        //Desinfeccion
        else if ($estado_cama_id == 3) {
            $tiempo_transcurrido = $diferencia->format('%h') . ':' . $diferencia->format('%i') . ':' . $diferencia->format('%s');
        }

        return $tiempo_transcurrido;
    }
}
