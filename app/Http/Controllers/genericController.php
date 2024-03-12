<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\Permission;
use App\Models\generic;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class geenericController extends Controller
{
    public $thisAtributos,$FromController = 'generic';


    //<editor-fold desc="Construc | mapea | filtro and dependencia">
    public function __construct() {
        $this->middleware('permission:create generic', ['only' => ['create', 'store']]);
        $this->middleware('permission:read generic', ['only' => ['index', 'show']]);
        $this->middleware('permission:update generic', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete generic', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new generic())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$generics = geeneric::with('dependex');
        $generics = geeneric::Where('id','>',0);
        return $generics;

    }
    public function Filtros(&$generics,$request){
        if ($request->has('search')) {
            $generics = $generics->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $generics = $generics->orderBy($request->field, $request->order);
        }else
            $generics = $generics->orderBy('updated_at', 'DESC');
    }
    public function Dependencias()
    {
        $dependexsSelect = deependex::all('id','nombre as name')->toArray();
        array_unshift($dependexsSelect,["name"=>"Seleccione un dependex",'id'=>0]);
        return $dependexsSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' generics '));
        $generics = $this->Mapear();
        $this->Filtros($generics,$request);
        $losSelect = $this->Dependencias();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $generics->paginate($perPage),
            'total'                 => $generics->count(),

            'breadcrumbs'           => [['label' => __('app.label.'.$this->FromController), 'href' => route($this->FromController.'.index')]],
            'title'                 => __('app.label.'.$this->FromController),
            'filters'               => $request->all(['search', 'field', 'order']),
            'perPage'               => (int) $perPage,
            'numberPermissions'     => $numberPermissions,
            'losSelect'             => $losSelect,
        ]);
    }

    public function create(){}

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function store(Request $request){
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:generics');
        DB::beginTransaction();
        $dependex = $request->dependex['id'];
        $request->merge(['dependex_id' => $request->dependex['id']]);
        $generic = generic::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:generics EXITOSO', 'generic id:' . $generic->id . ' | ' . $generic->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $generic->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:generics');
        DB::beginTransaction();
        $generic = generic::findOrFail($id);
        $request->merge(['dependex_id' => $request->dependex['id']]);
        $generic->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:generics EXITOSO', 'generic id:' . $generic->id . ' | ' . $generic->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $generic->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($genericid){
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:generics');
        $generic = generic::find($genericid);
        $elnombre = $generic->nombre;
        $generic->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:generics', 'generic id:' . $generic->id . ' | ' . $generic->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $generic = generic::whereIn('id', $request->id);
        $generic->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
