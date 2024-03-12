<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Actividad;
use App\helpers\Myhelp;

use App\helpers\HelpExcel;
use App\Http\Requests\ActividadRequest;
use App\Imports\PersonalImport;
use App\Models\Centrotrabajo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;


class ActividadsController extends Controller
{
    public string $nombreClase = 'Actividad';
    public string $MayusnombreClase = 'Actividad';
    public array $thisAtributos;

    public function __construct() {
        $this->thisAtributos = (new Actividad())->getFillable();
    }

    public function MapearClasePP(&$Actividads, $numberPermissions): void
    {

        $userA = Myhelp::AuthU();
        $Actividads = $Actividads->get()->map(function ($Actividad) use ($numberPermissions,$userA) {
            if($Actividad->empresa_id === $userA->empresa_id){
                $Actividad->centros = implode(',', $Actividad->centroTrabajos->pluck('nombre')->toArray());
                return $Actividad;
            }
        })->filter();
    }

    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this, $this->nombreClase);
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $user = Auth::user();

        // if($numberPermissions > 1){
        $Actividads = Actividad::query();
        // }else{
        //     $Actividads = Actividad::Where('operario_id',$user->id);
        // }

        if ($request->has('search')) {
            $Actividads->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    // ->orWhere('nombre', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }
        if ($request->has(['field', 'order']) && $request->field !== 'centros') {
            $Actividads = $Actividads->orderBy($request->field, $request->order);
        }else{
            $Actividads->orderByDesc('created_at');
        }
        $this->MapearClasePP($Actividads, $numberPermissions);

        // $losSelect = $this->SelectsMasivos($numberPermissions, $atributos_id);

        $perPage = $request->has('perPage') ? $request->perPage : 20;
        $total = $Actividads->count();
        $page = request('page', 1); // Current page number
        $fromController =  new LengthAwarePaginator(
            $Actividads->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        $centroSelect = Myhelp::NEW_turnInSelectID(Centrotrabajo::all(), 'centro','nombre');
        return Inertia::render('actividad/Index', [
            'breadcrumbs'           => [['label' => __('app.label.Actividad'), 'href' => route('actividad.index')]],
            'title'                 => __('app.label.Actividad'),
            'filters'               => $request->all(['search', 'field', 'order']),
            'perPage'               => (int) $perPage,
            'fromController'        => $fromController,
            'total'                 => $total,
            'numberPermissions'     => $numberPermissions,
            'losSelect'             => $centroSelect,

            // 'losSelect'             => $losSelect ?? [],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() { }

    //! STORE - UPDATE - DELETE
    public function store(ActividadRequest $request) {
        $user = Auth::User();
        Myhelp::EscribirEnLog($this, 'STORE:Actividads', '', false);

        DB::beginTransaction();
        try {
            $guardar = [];
            foreach ($this->thisAtributos as $value) {
                $guardar[$value] = $request->$value;
            }
            $guardar['tipo'] = $guardar['tipo']['value'];
            $guardar['codigo'] = 10;
            $guardar['centro_id'] = null;
            $Actividad = Actividad::create($guardar);
            foreach ($request->centro_id as $key => $value) {
                if($value['value'] && $value['value'] != 0)
                    $Actividad->centroTrabajos()->attach($value['value']);
            }

            DB::commit();
            Myhelp::EscribirEnLog($this, 'STORE:Actividads', 'usuario id:' . $user->id . ' | ' . $user->name . ' guardado', false);
            return back()->with('success', __('app.label.created_successfully', ['name' => $Actividad->name]));
        } catch (\Throwable $th) {
            DB::rollback();
            Myhelp::EscribirEnLog($this, 'STORE:Actividads', false);
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.Actividad')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }
    //fin store functions

    public function show($id) { } public function edit($id) { }


    public function update(ActividadRequest $request, $id) {
        $user = Auth::User();

        Myhelp::EscribirEnLog($this, 'UPGRADE:Actividads', '', false);
        DB::beginTransaction();
        try {
            $Actividad = Actividad::findOrFail($id);

            foreach ($this->thisAtributos as $value) {
                $guardar[$value] = $request->$value;
            }


            //arreglos
            $guardar['id'] = $id;
            if(isset($guardar['tipo']) && isset($guardar['tipo']['value'])){
                $guardar['tipo'] = $guardar['tipo']['value'];
            }

            foreach ($request->centro_id as $centro) {
                if($centro['value'] && $centro['value'] != 0){
                    $vectorCentros[] = $centro['value'];
                }
            }
            $Actividad->centroTrabajos()->sync($vectorCentros);

            $Actividad->update($guardar);

            DB::commit();
            Myhelp::EscribirEnLog($this, 'UPDATE:Actividads', 'usuario id:' . $Actividad->id . ' | ' . $Actividad->nombre . ' actualizado', false);

            return back()->with('success', __('app.label.updated_successfully', ['name' => $Actividad->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Myhelp::EscribirEnLog($this, 'UPDATE:Actividads', 'usuario id:' . $Actividad->id . ' | ' . $Actividad->nombre . '  fallo en el actualizado', false);
            return back()->with('error', __('app.label.updated_error', ['name' => $Actividad->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Actividad $Actividad){
        Myhelp::EscribirEnLog($this, 'DELETE:Actividads');
        try {
            $Actividad->delete();
            Myhelp::EscribirEnLog($this, 'DELETE:Actividads', 'usuario id:' . $Actividad->id . ' | ' . $Actividad->name . ' borrado', false);
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $Actividad->name]));

        }catch (QueryException $e) {
            $myHelp = new Myhelp();
            $mensajeError = $myHelp->mensajesErrorBD($e,$this->nombreClase,$Actividad->id,$Actividad->nombre);
            return back()->with('error', __('app.label.deleted_error', ['name' => $Actividad->nombre]) . $mensajeError);
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'DELETE:Reprocesos', 'usuario id:' . $Actividad->id . ' | ' . $Actividad->nombre . ' fallo en el borrado:: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile(), false);
            return back()->with('error', __('app.label.deleted_error', ['name' => $Actividad->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }

    public function destroyBulk(Request $request)
    {
        try {
            $Actividad = Actividad::whereIn('id', $request->id);
            $Actividad->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.Actividad')]));
        }catch (QueryException $e) {
            $myHelp = new Myhelp();
            $mensajeError = $myHelp->mensajesErrorBD($e,$this->nombreClase,$Actividad->id,$Actividad->nombre);
            return back()->with('error', __('app.label.deleted_error', ['name' => $Actividad->nombre]) . $mensajeError);
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'DELETE:Reprocesos', 'usuario id:' . $Actividad->id . ' | ' . $Actividad->nombre . ' fallo en el borrado:: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile(), false);
            return back()->with('error', __('app.label.deleted_error', ['name' => $Actividad->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }
    //FIN : STORE - UPDATE - DELETE

    public function subirexceles()
    { //just  a view
        $permissions = Myhelp::EscribirEnLog($this, ' Actividad');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        return Inertia::render('Actividad/subirExceles', [
            'breadcrumbs'   => [['label' => __('app.label.Actividad'), 'href' => route('Actividad.index')]],
            'title'         => __('app.label.Actividad'),
            'numUsuarios'   => count(Actividad::all()) - 1,
            // 'UniversidadSelect'   => Universidad::all()
        ]);
    }


    // Duplicate entry '1152194566' for key 'Actividads_identificacion_unique'
    private function MensajeWar()
    {
        $bandera = false;
        $contares = [
            'contar1',
            'contar2',
            'contar3',
            'contar4',
            'contar5',
            'contarVacios',
        ];
        $mensajesWarnings = [
            '#correos Existentes: ',
            'Novedad, error interno: ',
            '#cedulas no numericas: ',
            '#generos distintos(M,F,otro): ',
            '#identificaciones repetidas: ',
            '#filas con celdas vacias: ',
        ];

        foreach ($contares as $key => $value) {
            $$value = session($value, 0);
            session([$value => 0]);
            $bandera = $bandera || $$value > 0;
        }
        session(['contar2' => -1]);

        $mensaje = '';
        if ($bandera) {
            foreach ($mensajesWarnings as $key => $value) {
                if (${$contares[$key]} > 0) {
                    $mensaje .= $value . ${$contares[$key]} . '. ';
                }
            }
        }

        return $mensaje;
    }

    public function uploadtrabajadors(Request $request)
    {
        Myhelp::EscribirEnLog($this, get_called_class(), 'Empezo a importar', false);
        $countfilas = 0;
        try {
            if ($request->archivo1) {

                $helpExcel = new HelpExcel();
                $mensageWarning = $helpExcel->validarArchivoExcel($request);
                if ($mensageWarning != '') return back()->with('warning', $mensageWarning);

                Excel::import(new PersonalImport(), $request->archivo1);

                $countfilas = session('CountFilas', 0);
                session(['CountFilas' => 0]);

                $MensajeWarning = self::MensajeWar();
                if ($MensajeWarning !== '') {
                    return back()->with('success', 'Usuarios nuevos: ' . $countfilas)
                        ->with('warning', $MensajeWarning);
                }

                Myhelp::EscribirEnLog($this, 'IMPORT:Actividads', ' finalizo con exito', false);
                if ($countfilas == 0)
                    return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
                else
                    return back()->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito');
            } else {
                return back()->with('error', __('app.label.op_not_successfully') . ' archivo no seleccionado');
            }
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'IMPORT:Actividads', ' Fallo importacion: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile(), false);
            return back()->with('error', __('app.label.op_not_successfully') . ' Usuario del error: ' . session('larow')[0] . ' error en la iteracion ' . $countfilas . ' ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }
}
