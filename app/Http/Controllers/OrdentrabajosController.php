<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Requests\OrdentrabajoRequest;
use App\Models\Centrotrabajo;
use App\Models\Ordentrabajo;
use App\helpers\Myhelp;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrdentrabajosController extends Controller
{
    public $nombreClase = 'ordentrabajo';
    public $MayusnombreClase = 'Ordentrabajo';
    public $thisAtributos;


    public function __construct() {
        $this->thisAtributos = (new Ordentrabajo())->getFillable();
    }

    public function MapearClasePP(&$Ordentrabajos, $numberPermissions) {
        $userA = Myhelp::AuthU();
        $Ordentrabajos = $Ordentrabajos->get()->map(function ($Ordentrabajo) use ($numberPermissions,$userA) {
            if($Ordentrabajo->empresa_id == $userA->empresa_id){
                return $Ordentrabajo;
            }
//            $Ordentrabajo->centros = implode(',', $Ordentrabajo->centroTrabajos->pluck('nombre')->toArray());
        })->filter();
    }

    public function index(Request $request) {
        $permissions = Myhelp::EscribirEnLog($this, $this->nombreClase);
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);
        $user = Auth::user();

        // if($numberPermissions > 1){
        $Ordentrabajos = Ordentrabajo::query();
        // }else{
        //     $Ordentrabajos = Ordentrabajo::Where('operario_id',$user->id);
        // }

        if ($request->has('search')) {
            $Ordentrabajos->where(function ($query) use ($request) {
                $query->where('codigo', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
            });
        }
        if ($request->has(['field', 'order']) && $request->field != 'centros') {
            $Ordentrabajos = $Ordentrabajos->orderBy($request->field, $request->order);
        }else{
            $Ordentrabajos->orderByDesc('created_at');
        }
        $this->MapearClasePP($Ordentrabajos, $numberPermissions);

        // $losSelect = $this->SelectsMasivos($numberPermissions, $atributos_id);

        $perPage = $request->has('perPage') ? $request->perPage : 20;
        $total = $Ordentrabajos->count();
        $page = request('page', 1); // Current page number
        $fromController =  new LengthAwarePaginator(
            $Ordentrabajos->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        $centroSelect = Myhelp::NEW_turnInSelectID(Centrotrabajo::all(), 'centro','nombre');

        return Inertia::render('ordentrabajo/Index', [
            'breadcrumbs'           => [['label' => __('app.label.ordentrabajo'), 'href' => route('ordentrabajo.index')]],
            'title'                 => __('app.label.ordentrabajo'),
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
    public function create(){}

    //! STORE - UPDATE - DELETE
    public function store(OrdentrabajoRequest $request){
        $user = Auth::User();
        Myhelp::EscribirEnLog($this, 'STORE:Ordentrabajos', '', false);

        DB::beginTransaction();
        try {
            $guardar = [];
            foreach ($this->thisAtributos as $value) {
                $guardar[$value] = $request->$value;
            }
//            $guardar['tipo'] = $guardar['tipo']['value'];
//            $guardar['codigo'] = 10;
            $Ordentrabajo = Ordentrabajo::create($guardar);
//            foreach ($request->centro_id as $key => $value) {
//                if($value['value'] && $value['value'] != 0)
//                    $Ordentrabajo->centroTrabajos()->attach($value['value']);
//            }
            DB::commit();
            Myhelp::EscribirEnLog($this, 'STORE:Ordentrabajos', 'usuario id:' . $user->id . ' | ' . $user->name . ' guardado', false);
            return back()->with('success', __('app.label.created_successfully', ['name' => $Ordentrabajo->name]));
        } catch (\Throwable $th) {
            DB::rollback();
            Myhelp::EscribirEnLog($this, 'STORE:Ordentrabajos', false);
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.Ordentrabajo')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }
    //fin store functions

    public function show($id){}public function edit($id){}


    public function update(OrdentrabajoRequest $request, $id) {

        Myhelp::EscribirEnLog($this, 'UPGRADE:Ordentrabajos', '', false);
        DB::beginTransaction();
        try {
            $Ordentrabajo = Ordentrabajo::findOrFail($id);

            foreach ($this->thisAtributos as $value) {
                $guardar[$value] = $request->$value;
            }

            //arreglos
            $guardar['id'] = $id;

//            if(isset($guardar['tipo']) && isset($guardar['tipo']['value'])){
//                $guardar['tipo'] = $guardar['tipo']['value'];
//            }
//            foreach ($request->centro_id as $centro) {
//                if($centro['value'] && $centro['value'] != 0){
//                    $vectorCentros[] = $centro['value'];
//                }
//            }
//            $Ordentrabajo->centroTrabajos()->sync($vectorCentros);
            $Ordentrabajo->update($guardar);

            DB::commit();
            Myhelp::EscribirEnLog($this, 'UPDATE:Ordentrabajos', 'usuario id:' . $Ordentrabajo->id . ' | ' . $Ordentrabajo->nombre . ' actualizado', false);

            return back()->with('success', __('app.label.updated_successfully', ['name' => $Ordentrabajo->nombre]));
        } catch (\Throwable $th) {
            DB::rollback();
            Myhelp::EscribirEnLog($this, 'UPDATE:Ordentrabajos', 'usuario id:' . $Ordentrabajo->id . ' | ' . $Ordentrabajo->nombre . '  fallo en el actualizado', false);
            return back()->with('error', __('app.label.updated_error', ['name' => $Ordentrabajo->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }

    public function destroy(Ordentrabajo $Ordentrabajo){
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:Ordentrabajos');
        try {
            Myhelp::EscribirEnLog($this, 'DELETE:Ordentrabajos', 'usuario id:' . $Ordentrabajo->id . ' | ' . $Ordentrabajo->nombre . ' borrado', false);
            $Ordentrabajo->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $Ordentrabajo->nombre]));

        }catch (QueryException $e) {
            $myHelp = new Myhelp();
            $mensajeError = $myHelp->mensajesErrorBD($e,$this->nombreClase,$Ordentrabajo->id,$Ordentrabajo->nombre);
            Myhelp::EscribirEnLog($this, 'DELETE:Ordentrabajos '. $mensajeError);
            return back()->with('error', __('app.label.deleted_error', ['name' => $Ordentrabajo->nombre]) . $mensajeError);
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'DELETE:Ordentrabajos Throwable', 'ordentrabajo id:' . $Ordentrabajo->id . ' | ' . $Ordentrabajo->nombre . ' fallo en el borrado:: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile(), false);
            return back()->with('error', __('app.label.deleted_error', ['name' => $Ordentrabajo->nombre]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }

    public function destroyBulk(Request $request){
        try {
            $Ordentrabajo = Ordentrabajo::whereIn('id', $request->id);
            $Ordentrabajo->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.Ordentrabajo')]));
        } catch (\Throwable $th) {
            $mensajeFinal =  $th->getMessage() . ' Linea: ' . $th->getLine() . ' Ubi: ' . $th->getFile();
            Myhelp::EscribirEnLog($this, 'DELETE:Ordentrabajos '. $mensajeFinal);
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.Ordentrabajo')]) .$mensajeFinal);
        }
    }
    //FIN : STORE - UPDATE - DELETE
}
