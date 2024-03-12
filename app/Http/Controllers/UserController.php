<?php

namespace App\Http\Controllers;

use App\helpers\HelpExcel;
use App\helpers\Myhelp;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Imports\PersonalImport;
use App\Models\Empresa;
use App\Models\Permission;
use App\Models\Reporte;
use App\Models\Role;
use App\Models\User;
use App\Exports\MultipleExport;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller {
    public $thisAtributos;

    public function __construct() {
        $this->middleware('permission:create user', ['only' => ['create', 'store']]);
        $this->middleware('permission:read user', ['only' => ['index', 'show']]);
        $this->middleware('permission:update user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete user', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new User())->getFillable(); //not using

    }

    public function Dashboard() {
        $numberPermissions = Myhelp::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Dashboard'));
        if($numberPermissions > 1){

            return Inertia::render('Dashboard', [
                'users'         => (int) User::count(),
                'roles'         => (int) Role::count(),
                'reportes'      => (int) Reporte::count(),
                'permissions'   => (int) Permission::count(),
            ]);
        }else{
            return redirect()->route('reporte.index');
        }

    }
    public function SelectsMasivos($numberPermissions) {
//        $empresatemp = new empresa();
        $modelInstance = resolve('App\\Models\\' . ucfirst('empresa'));
        $ultima = $modelInstance::All();
        $result['empresa'] = Myhelp::NEW_turnInSelectID($ultima, ' ');

        return $result;
    }

    public function MapearClasePP(&$users, $numberPermissions,$request,&$roles)
    {
//        $role = auth()->user()->roles->pluck('name')[0];
        $roles = Role::where('name', '<>', 'superadmin')->where('name', '<>', 'admin')->get();
        if ($numberPermissions < 9) {
            $users->whereHas('roles', function ($query) {
                return $query->whereNotIn('name', ['superadmin', 'admin']);
            });
        } else {
            if ($numberPermissions === 9) {
                $users->whereHas('roles', function ($query) {
                    return $query->where('name', '<>', 'superadmin');
                });
            }
            $roles = Role::get();
        }


        $userA = Myhelp::AuthU();
        if ($request->has(['field', 'order'])) {
            $users = $users->orderBy($request->field, $request->order);
        }else{
            $users = $users->orderBy('updated_at', 'desc');
        }

        if($numberPermissions < 9){
            $users = $users->get()->map(function ($user) use ($userA) {
                if($user->empresa_id == $userA->empresa_id){
                    $user->empresa = $user->empresa()->first()->nombre;
                    return $user;
                }
            })->filter();
        }else{

            $users = $users->get()->map(function ($user) use ($userA) {
                $user->empresa = $user->empresa()->first()->nombre;
                return $user;
            })->filter();
        }
    }

    public function index(UserIndexRequest $request) {
        $permissions = Myhelp::EscribirEnLog($this, ' users');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        $users = User::query()->with('roles');
        $this->MapearClasePP($users,$numberPermissions,$request,$roles);
        if ($request->has('search')) {
            $users->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('email', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%");
            })->where('name', '!=', 'admin')->where('name', '!=', 'Superadmin');
            // $users->where('name', 'LIKE', "%" . $request->search . "%");
        }

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $total = $users->count();
        $page = request('page', 1); // Current page number
        $fromController =  new LengthAwarePaginator($users->forPage($page, $perPage), $total, $perPage, $page, ['path' => request()->url()]);

        $losSelect = $this->SelectsMasivos($numberPermissions);
        return Inertia::render('User/Index', [
            'breadcrumbs'           => [['label' => __('app.label.user'), 'href' => route('user.index')]],
            'title'                 => __('app.label.user'),
            'filters'               => $request->all(['search', 'field', 'order']),
            'perPage'               => (int) $perPage,
            'users'                 => $fromController,
            'roles'                 => $roles,
            'numberPermissions'     => $numberPermissions,
            'losSelect'             => $losSelect,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){}

    //! STORE - UPDATE - DELETE
    public function store(UserStoreRequest $request){
        $permissions = Myhelp::EscribirEnLog($this, 'STORE:users');
        $user = Auth::user();
        DB::beginTransaction();
        try {
            if(isset($request->sexo['value'])){
                $sexo = is_string($request->sexo) ? $request->sexo : $request->sexo['value'];
            }else{
                $sexo = 'Masculino';
            }

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'area'     => $request->area,
                'cargo'     => $request->cargo,
                'identificacion' => $request->identificacion,
                'celular' => $request->celular,
                'sexo' => $sexo,
                'fecha_nacimiento' => Myhelp::updatingDate($request->fecha_nacimiento),
                'password' => Hash::make($request->identificacion . '*'),
            ]);
            $user->assignRole($request->role);
            DB::commit();
            Myhelp::EscribirEnLog($this, 'STORE:users', 'usuario id:' . $user->id . ' | ' . $user->name . ' guardado', false);

            return back()->with('success', __('app.label.created_successfully', ['name' => $user->name]));
        } catch (\Throwable $th) {
            DB::rollback();
            Myhelp::EscribirEnLog($this, 'STORE:users', 'usuario id:' . $user->id . ' | ' . $user->name . ' fallo en el guardado', false);
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.user')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }
    //fin store functions
    public function show($id){}public function edit($id){}

    public function update(UserUpdateRequest $request, $id){
        Myhelp::EscribirEnLog($this, 'UPDATE:users', '', false);
        DB::beginTransaction();
        try {
            $sexo = is_string($request->sexo) ? $request->sexo : $request->sexo['value'];
            $user = User::findOrFail($id);
            $empresaID = is_null($request->empresa_id) ? $user->empresa_id : $request->empresa_id['value'];
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'area'     => $request->area,
                'cargo'     => $request->cargo,
                'identificacion' => $request->identificacion,
                'celular' => $request->celular,
                'sexo' => $sexo,
                'fecha_nacimiento' => Myhelp::updatingDate($request->fecha_nacimiento),
                'empresa_id' => $empresaID,
            ]);

            $user->syncRoles($request->role);
            DB::commit();
            Myhelp::EscribirEnLog($this, 'UPDATE:users', 'usuario id:' . $user->id . ' | ' . $user->name . ' actualizado', false);
            return back()->with('success', __('app.label.updated_successfully', ['name' => $user->name]));
        } catch (\Throwable $th) {
            DB::rollback();
            Myhelp::EscribirEnLog($this, 'UPDATE:users', 'usuario id:' . $user->id . ' | ' . $user->name . '  fallo en el actualizado', false);
            return back()->with('error', __('app.label.updated_error', ['name' => $user->name]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:users');

        try {
            $user->delete();
            Myhelp::EscribirEnLog($this, 'DELETE:users', 'usuario id:' . $user->id . ' | ' . $user->name . ' borrado', false);
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $user->name]));
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'DELETE:users', 'usuario id:' . $user->id . ' | ' . $user->name . ' fallo en el borrado:: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile(), false);
            return back()->with('error', __('app.label.deleted_error', ['name' => $user->name]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }

    public function destroyBulk(Request $request)
    {
        try {
            $user = User::whereIn('id', $request->id);
            $user->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
        } catch (\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.user')]) . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }
    //FIN : STORE - UPDATE - DELETE

    public function subirexceles()
    { //just  a view
        $permissions = Myhelp::EscribirEnLog($this, ' materia');
        $numberPermissions = Myhelp::getPermissionToNumber($permissions);

        return Inertia::render('User/subirExceles', [
            'breadcrumbs'   => [['label' => __('app.label.user'), 'href' => route('user.index')]],
            'title'         => __('app.label.user'),
            'numUsuarios'   => count(User::all()) - 1,
            // 'UniversidadSelect'   => Universidad::all()
        ]);
    }


    // Duplicate entry '1152194566' for key 'users_identificacion_unique'
    private function MensajeWar(){
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

    public function uploadtrabajadors(Request $request){
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

                $MensajeWarning = $this->MensajeWar();
                if ($MensajeWarning !== '') {
                    return back()->with('success', 'Usuarios nuevos: ' . $countfilas)
                        ->with('warning', $MensajeWarning);
                }

                Myhelp::EscribirEnLog($this, 'IMPORT:users', ' finalizo con exito', false);
                if ($countfilas == 0)
                    return back()->with('success', __('app.label.op_successfully') . ' No hubo cambios');
                else
                    return back()->with('success', __('app.label.op_successfully') . ' Se leyeron ' . $countfilas . ' filas con exito');
            } else {
                return back()->with('error', __('app.label.op_not_successfully') . ' archivo no seleccionado');
            }
        } catch (\Throwable $th) {
            Myhelp::EscribirEnLog($this, 'IMPORT:users', ' Fallo importacion: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile(), false);
            return back()->with('error', __('app.label.op_not_successfully') . ' Usuario del error: ' . session('larow')[0] . ' error en la iteracion ' . $countfilas . ' ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile());
        }
    }

    public function todaBD(){
//        return (new MultipleExport())->download('toma_tiemposDB.xlsx');
        return Excel::download(new MultipleExport, 'toma_tiemposDB.xlsx');

    }

    public function RRepor(){
        $usuariosConUltimoReporte = User::has('reportes')->get();
        $reportes = [];
        foreach ($usuariosConUltimoReporte as $item) {
            $elmodelo = $item->reportes->first();
            if($elmodelo){
                $reportes[] = [
                    $item->name,
                    $elmodelo->fecha,
                    $elmodelo->hora_inicial,
                    $elmodelo->hora_final,
                ];
            }
        }

        $reportesu = Reporte::all();
        foreach ($reportesu as $index => $reporte) {
            if($reporte->hora_final){
                $horaInicial = Carbon::parse($reporte->hora_inicial);
                $horafinal = Carbon::parse($reporte->hora_final);
                $tiemtras = number_format($horafinal->diffInSeconds($horaInicial)/3600,3);
                $repor = [
                    'tiempo_transcurrido' => $tiemtras
                ];
                $reporte->update($repor);
            }
        }

        dd(
            $reportes,
            $reportes[0],
            $reportes[1] ?? 'no existe',
            $reportes[2] ?? 'no existe',
            $reportes[3] ?? 'no existe',
        );

    }
}
