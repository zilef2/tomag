<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Http\Requests\EmpresaRequest;
use App\Models\Permission;
use App\Models\empresa;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class EmpresaController extends Controller{
    public $thisAtributos,$FromController = 'empresa';

    //<editor-fold desc="Construc | mapea | filtro and dependencia">
    public function __construct() {
//        $this->middleware('permission:create empresa', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read empresa', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update empresa', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete empresa', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new empresa())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$empresas = Empresa::with('no_nada');
        return Empresa::Where('id','>',0);
    }

    public function Filtros(&$empresas,$request){
        if ($request->has('search')) {
            $empresas = $empresas->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }
        if ($request->has(['field', 'order'])) {
            $empresas = $empresas->orderBy($request->field, $request->order);
        }else
            $empresas = $empresas->orderBy('updated_at', 'DESC');
    }

    public function Dependencias(){
//        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
//        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
//        return $no_nadasSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' empresas '));
        $empresas = $this->Mapear();
        $this->Filtros($empresas,$request);
//        $losSelect = $this->Dependencias();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $empresas->paginate($perPage),
            'total'                 => $empresas->count(),

            'breadcrumbs'           => [['label' => __('app.label.'.$this->FromController), 'href' => route($this->FromController.'.index')]],
            'title'                 => __('app.label.'.$this->FromController),
            'filters'               => $request->all(['search', 'field', 'order']),
            'perPage'               => (int) $perPage,
            'numberPermissions'     => $numberPermissions,
//            'losSelect'             => $losSelect,
        ]);
    }

    public function create(){}

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function store(EmpresaRequest $request){
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:empresas');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $empresa = empresa::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:empresas EXITOSO', 'empresa id:' . $empresa->id . ' | ' . $empresa->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $empresa->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:empresas');
        DB::beginTransaction();
        $empresa = empresa::findOrFail($id);
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $empresa->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:empresas EXITOSO', 'empresa id:' . $empresa->id . ' | ' . $empresa->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $empresa->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($empresaid){
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:empresas');
        $empresa = empresa::find($empresaid);
        $elnombre = $empresa->nombre;
        $empresa->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:empresas', 'empresa id:' . $empresa->id . ' | ' . $empresa->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $empresa = empresa::whereIn('id', $request->id);
        $empresa->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
