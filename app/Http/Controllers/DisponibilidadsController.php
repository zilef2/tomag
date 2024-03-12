<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Disponibilidad;
use App\helpers\Myhelp;

use App\helpers\HelpExcel;
use App\Http\Requests\DisponibilidadRequest;
use App\Imports\PersonalImport;
use App\Models\Centrotrabajo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class DisponibilidadsController extends Controller
{
    public $nombreClase = 'Disponibilidad';
    public $MayusnombreClase = 'Disponibilidad';
    public $thisAtributos;

    public function __construct() {
        $this->thisAtributos = (new Disponibilidad())->getFillable();
    }

    public function MapearClasePP(&$Disponibilidads, $numberPermissions) {
        $userA = Myhelp::AuthU();
        $Disponibilidads = $Disponibilidads->get()->map(function ($Disponibilidad) use ($numberPermissions,$userA) {
            if($Disponibilidad->empresa_id == $userA->empresa_id){
                $Disponibilidad->centros = implode(',', $Disponibilidad->centroTrabajos->pluck('nombre')->toArray());
                return $Disponibilidad;
            }
        })->filter();
    }

    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this, $this->nombreClase);
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $user = Auth::user();

        // if($numberPermissions > 1){
        $Disponibilidads = Disponibilidad::query();
        // }else{
        //     $Disponibilidads = Disponibilidad::Where('operario_id',$user->id);
        // }

        if ($request->has('search')) {
            $Disponibilidads->where(function ($query) use ($request) {
                $query->where('codigo', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
            });
        }
        if ($request->has(['field', 'order']) && $request->field != 'centros') {
            $Disponibilidads = $Disponibilidads->orderBy($request->field, $request->order);
        }else{
            $Disponibilidads->orderByDesc('created_at');
        }
        $this->MapearClasePP($Disponibilidads, $numberPermissions);

        // $losSelect = $this->SelectsMasivos($numberPermissions, $atributos_id);

        $perPage = $request->has('perPage') ? $request->perPage : 20;
        $total = $Disponibilidads->count();
        $page = request('page', 1); // Current page number
        $fromController =  new LengthAwarePaginator(
            $Disponibilidads->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        $centroSelect = Myhelp::NEW_turnInSelectID(Centrotrabajo::all(), 'centro','nombre');

        return Inertia::render('disponibilidad/Index', [
            'breadcrumbs'           => [['label' => __('app.label.disponibilidad'), 'href' => route('disponibilidad.index')]],
            'title'                 => __('app.label.disponibilidad'),
            'filters'               => $request->all(['search', 'field', 'order']),
            'perPage'               => (int) $perPage,
            'fromController'        => $fromController,
            'total'                 => $total,
            'numberPermissions'     => $numberPermissions,
            'losSelect'             => $centroSelect,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }


    //! STORE - UPDATE - DELETE
    public function store(DisponibilidadRequest $request){
        $user = Auth::User();
        Myhelp::EscribirEnLog($this, 'STORE:Disponibilidads', '', false);

        DB::beginTransaction();
        try {
            $guardar = [];
            foreach ($this->thisAtributos as $value) {
                $guardar[$value] = $request->$value;
            }
            $guardar['tipo'] = $guardar['tipo']['value'];
            $guardar['centro_id'] = null;
            $guardar['codigo'] = 10;
            $Disponibilidad = Disponibilidad::create($guardar);
            foreach ($request->centro_id as $key => $value) {
                if($value['value'] && $value['value'] != 0)
                    $Disponibilidad->centroTrabajos()->attach($value['value']);
            }
            DB::commit();
            Myhelp::EscribirEnLog($this, 'STORE:Disponibilidads', 'usuario id:' . $user->id . ' | ' . $user->name . ' guardado', false);
            return back()->with('success', __('app.label.created_successfully', ['name' => $Disponibilidad->name]));
        } catch (\Throwable $th) {
            DB::rollback();
            Myhelp::EscribirEnLog($this, 'STORE:Disponibilidads', false);
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.Disponibilidad')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }
    //fin store functions

    public function show($id){}public function edit($id){}


    public function update(DisponibilidadRequest $request, $id) {

        Myhelp::EscribirEnLog($this, 'UPGRADE:Disponibilidads', '', false);
        DB::beginTransaction();
        try {
            $Disponibilidad = Disponibilidad::findOrFail($id);

            foreach ($this->thisAtributos as $value) {
                $guardar[$value] = $request->$value;
            }

            //arreglos
            $guardar['id'] = $id;

            if(isset($guardar['tipo']) && isset($guardar['tipo']['value'])){
                $guardar['tipo'] = $guardar['tipo']['value'];
            }
//            dd($request->centro_id);
            foreach ($request->centro_id as $centro) {
                if($centro['value'] && $centro['value'] != 0){
                    $vectorCentros[] = $centro['value'];
                }
            }
            $Disponibilidad->centroTrabajos()->sync($vectorCentros);


            $Disponibilidad->update($guardar);

            DB::commit();
            Myhelp::EscribirEnLog($this, 'UPDATE:Disponibilidads', 'usuario id:' . $Disponibilidad->id . ' | ' . $Disponibilidad->nombre . ' actualizado', false);

            return back()->with('success', __('app.label.updated_successfully', ['name' => $Disponibilidad->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Myhelp::EscribirEnLog($this, 'UPDATE:Disponibilidads', 'usuario id:' . $Disponibilidad->id . ' | ' . $Disponibilidad->nombre . '  fallo en el actualizado', false);
            return back()->with('error', __('app.label.updated_error', ['name' => $Disponibilidad->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }

    public function destroy(Disponibilidad $Disponibilidad){
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:Disponibilidads');
        try {
            Myhelp::EscribirEnLog($this, 'DELETE:Disponibilidads', 'usuario id:' . $Disponibilidad->id . ' | ' . $Disponibilidad->nombre . ' borrado', false);
            $Disponibilidad->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $Disponibilidad->nombre]));

        }catch (QueryException $e) {
            $myHelp = new Myhelp();
            $mensajeError = $myHelp->mensajesErrorBD($e,$this->nombreClase,$Disponibilidad->id,$Disponibilidad->nombre);
            Myhelp::EscribirEnLog($this, 'DELETE:Disponibilidads '. $mensajeError);
            return back()->with('error', __('app.label.deleted_error', ['name' => $Disponibilidad->nombre]) . $mensajeError);
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'DELETE:Disponibilidads Throwable', 'disponibilidad id:' . $Disponibilidad->id . ' | ' . $Disponibilidad->nombre . ' fallo en el borrado:: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile(), false);
            return back()->with('error', __('app.label.deleted_error', ['name' => $Disponibilidad->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }

    public function destroyBulk(Request $request){
        try {
            $Disponibilidad = Disponibilidad::whereIn('id', $request->id);
            $Disponibilidad->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.Disponibilidad')]));
        } catch (\Throwable $th) {
            $mensajeFinal =  $th->getMessage() . ' Linea: ' . $th->getLine() . ' Ubi: ' . $th->getFile();
            Myhelp::EscribirEnLog($this, 'DELETE:Disponibilidads '. $mensajeFinal);
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.Disponibilidad')]) .$mensajeFinal);
        }
    }
    //FIN : STORE - UPDATE - DELETE
}
