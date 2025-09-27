<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Validation\Rule;
use App\Models\Configuration;
use App\Models\User;

class RolesController extends Controller
{
    public function index()
    {
        $configurations = Configuration::all()->pluck('value', 'key')->toArray();

        return view('roles', compact('configurations'));
    }
    public function setUserRole(Request $request, $userId)
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'guard'   => ['nullable', 'string'],
        ], [
            'role_id.required' => 'Debes indicar el rol a asignar.',
            'role_id.exists'   => 'El rol indicado no existe.',
        ]);

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.'
            ], 404);
        }

        $role = Role::find($data['role_id']);
        $userGuard = config('auth.defaults.guard', 'web');

        $guardToUse = $data['guard'] ?? $userGuard;

        if ($role->guard_name !== $guardToUse) {
            return response()->json([
                'success' => false,
                'message' => 'El guard del rol no coincide con el guard del usuario.'
            ], 422);
        }

        // 🔄 Quita todos los roles y asigna el nuevo
        $user->syncRoles([$role]);

        return response()->json([
            'success' => true,
            'message' => "Rol asignado correctamente.",
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'role' => [
                'id'         => $role->id,
                'name'       => $role->name,
                'guard_name' => $role->guard_name,
            ],
        ]);
    }
    public function rolesDeUsuario(Request $request, $userId)
    {
        $defaultGuard = config('auth.defaults.guard', 'web');
        $guard = $request->query('guard', $defaultGuard);
        $includePermissions = $request->boolean('include_permissions', false);

        // Traer usuario con roles filtrados por guard
        $user = User::with(['roles' => function ($q) use ($guard) {
            $q->where('guard_name', $guard)
                ->select('id', 'name', 'guard_name');
        }])->find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.'
            ], 404);
        }

        $roles = $user->roles->map(fn($r) => [
            'id'         => $r->id,
            'name'       => $r->name,
            'guard_name' => $r->guard_name,
        ])->values();

        $response = [
            'success' => true,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'guard' => $guard,
            'roles' => $roles,
        ];

        if ($includePermissions) {
            // Permisos efectivo via roles (únicos), filtrados por guard
            $permissions = $user->getPermissionsViaRoles()
                ->where('guard_name', $guard)
                ->unique('id')
                ->sortBy('name')
                ->values()
                ->map(fn($p) => [
                    'id'   => $p->id,
                    'name' => $p->name,
                ]);

            $response['permissions_via_roles'] = $permissions;
        }

        return response()->json($response);
    }

    public function permisos(Request $request)
    {
        $query = Permission::query()->select('id', 'name', 'guard_name', 'created_at');

        // Solo "views" (por convención: ver.*)
        if ($request->string('scope')->lower() === 'views') {
            $query->where('name', 'like', 'ver.%');
        }

        // Búsqueda simple
        if ($search = $request->string('q')->toString()) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Orden por nombre asc por defecto
        $permisos = $query->orderBy('name')->get();

        // Formato simple para DataTables (dataSrc: '')
        return response()->json($permisos);
    }

    public function store(Request $request)
    {
        // Validación
        $data = $request->validate([
            'nombre'               => 'required|string|max:100|unique:roles,name',
            'permisos_id'          => 'required|array|min:1',
            'permisos_id.*'        => 'integer|exists:permissions,id',
        ], [
            'nombre.required'      => 'El nombre es obligatorio.',
            'nombre.unique'        => 'Ya existe un rol con ese nombre.',
            'permisos_id.required' => 'Debes seleccionar al menos un permiso.',
            'permisos_id.*.exists' => 'Algún permiso seleccionado no existe.',
        ]);

        // Limpia la caché de permisos/roles de Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Crear el rol y asignar permisos en transacción
        $role = DB::transaction(function () use ($data) {
            // Crear rol (guard por defecto: web)
            $role = Role::create([
                'name'       => $data['nombre'],
                'guard_name' => 'web',
            ]);

            // Buscar permisos por ID y asignarlos
            $permisos = Permission::whereIn('id', $data['permisos_id'])->get();
            $role->syncPermissions($permisos); // o ->givePermissionTo($permisos)

            return $role;
        });

        // Opcional: volver a limpiar caché
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => 'Rol creado correctamente.',
            'rol'     => [
                'id'         => $role->id,
                'name'       => $role->name,
                'permisos'   => $role->permissions()->pluck('name'),
            ],
        ], 201);
    }
    public function allRoles()
    {
        $roles = Role::with('permissions:id,name') // Incluye los permisos del rol
            ->select('id', 'name', 'guard_name', 'created_at')
            ->orderBy('name')
            ->get();

        return response()->json($roles);
    }

    public function show($id)
    {
        $rol = Role::with('permissions:id,name')
            ->select('id', 'name', 'guard_name', 'created_at')
            ->find($id);

        if (!$rol) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado.'
            ], 404);
        }

        return response()->json([
            'success'    => true,
            'id'         => $rol->id,
            'name'       => $rol->name,
            'guard_name' => $rol->guard_name,
            'created_at' => $rol->created_at,
            'permisos'   => $rol->permissions->map(function ($permiso) {
                return [
                    'id'   => $permiso->id,
                    'name' => $permiso->name,
                ];
            })
        ]);
    }
    public function update(Request $request, $id)
    {
        // Buscar rol
        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado.'
            ], 404);
        }

        // Validación (unique ignorando el ID actual)
        $data = $request->validate([
            'nombre'        => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')->ignore($role->id)
            ],
            'permisos_id'   => 'required|array|min:1',
            'permisos_id.*' => 'integer|exists:permissions,id',
        ], [
            'nombre.required'      => 'El nombre es obligatorio.',
            'nombre.unique'        => 'Ya existe un rol con ese nombre.',
            'permisos_id.required' => 'Debes seleccionar al menos un permiso.',
            'permisos_id.*.exists' => 'Algún permiso seleccionado no existe.',
        ]);

        // Limpiar caché de Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Actualizar rol y sincronizar permisos
        $role = DB::transaction(function () use ($role, $data) {
            $role->update([
                'name' => $data['nombre'],
            ]);

            $permisos = Permission::whereIn('id', $data['permisos_id'])->get();
            $role->syncPermissions($permisos);

            return $role;
        });

        // (Opcional) limpiar caché nuevamente
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'success'  => true,
            'message'  => 'Rol actualizado correctamente.',
            'rol'      => [
                'id'       => $role->id,
                'name'     => $role->name,
                'permisos' => $role->permissions->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                ]),
            ],
        ]);
    }

    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado.'
            ], 404);
        }

        // 1) Obtener el ID del rol por defecto desde configuraciones
        $defaultRoleId = (int) \App\Models\Configuration::getValue('rol_defecto'); // ajustá namespace si difiere

        if (!$defaultRoleId) {
            return response()->json([
                'success' => false,
                'message' => 'No hay rol por defecto configurado (config: rol_defecto).'
            ], 422);
        }

        // 2) Buscar el rol por defecto
        $defaultRole = Role::find($defaultRoleId);
        if (!$defaultRole) {
            return response()->json([
                'success' => false,
                'message' => 'El rol por defecto configurado no existe.'
            ], 422);
        }

        // 3) No permitir eliminar el rol por defecto actual
        if ($defaultRole->id === $role->id) {
            return response()->json([
                'success' => false,
                'message' => 'No podés eliminar el rol por defecto. Cambiá la configuración y reintentá.'
            ], 422);
        }

        // (Opcional) Si usás múltiples guards, asegurate que coincidan
        if ($defaultRole->guard_name !== $role->guard_name) {
            return response()->json([
                'success' => false,
                'message' => 'El rol por defecto pertenece a otro guard y no puede asignarse a estos usuarios.'
            ], 422);
        }

        // Limpiar cache de Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $reasignados = 0;

        DB::transaction(function () use ($role, $defaultRole, &$reasignados) {
            // 4) Reasignar a todos los usuarios que tenían el rol a eliminar
            $role->users()->chunkById(500, function ($users) use ($role, $defaultRole, &$reasignados) {
                foreach ($users as $user) {
                    // quitar el rol viejo
                    $user->removeRole($role);
                    // asignar el default (assignRole ignora duplicados)
                    $user->assignRole($defaultRole);
                    $reasignados++;
                }
            });

            // 5) Eliminar el rol
            $role->delete();
        });

        // (Opcional) limpiar cache otra vez
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'success'      => true,
            'message'      => "Rol eliminado. Usuarios reasignados al rol por defecto: {$reasignados}.",
            'reasignados'  => $reasignados,
            'rol_defecto'  => ['id' => $defaultRole->id, 'name' => $defaultRole->name],
        ]);
    }


    public function dataTom(Request $request)
    {


        $query = Role::query();



        // Si no hay $q, esto devuelve todos los clientes (o límite para no saturar)
        $clientes = $query->get();

        return response()->json(
            $clientes->map(function ($cliente) {
                return [
                    'value' => $cliente->id,
                    'text'  => "{$cliente->name}",
                ];
            })
        );
    }
}