<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class RoleController extends Controller
{
<<<<<<< Updated upstream
    public function __construct() { 

        $this->authorizeResource(Role::class, 'role'); 
    }

=======
        /**
     * @OA\Get(
     *     path="/v1/roles",
     *     summary="Listar todos los roles",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="trashed",
     *         in="query",
     *         required=false,
     *         description="Mostrar roles eliminados",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         description="Filtrar por nombre del rol",
     *         @OA\Schema(type="string", example="Admin")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Admin")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
>>>>>>> Stashed changes
    public function index(Request $request)
    {
        $roles = Role::query();

        if ($request->boolean('trashed')) {
            $roles = $roles->onlyTrashed()->get();
        } else {
            $roles = $roles
                ->when($request->has('name'), function ($query) use ($request) {
                    $query->where('name', 'like', $request->input('name') . '%');
                })->get();
        } 
        return response()->json(RoleResource::collection($roles), 200);
    }

<<<<<<< Updated upstream
=======
    /**
     * @OA\Post(
     *     path="/v1/roles",
     *     summary="Crear un nuevo rol",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=100, example="Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rol creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ya existe ese rol."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="Ya existe ese rol.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    /**
     * Store a newly created resource in storage.
     */
>>>>>>> Stashed changes
    public function store(RoleRequest $request)
    {
        $data = $request->validated();

        $role = Role::create($data);
        $role->refresh();

        return response()->json(RoleResource::make($role), 201);
    }

<<<<<<< Updated upstream
    public function show(Role $role)
=======
    /**
     * @OA\Get(
     *     path="/v1/roles/{rol}",
     *     summary="Ver detalle de un rol",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="rol",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle del rol obtenido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Role].")
     *         )
     *     )
     * )
     */

    /**
     * Display the specified resource.
     */
    public function show(Role $rol)
>>>>>>> Stashed changes
    {
        return response()->json(RoleResource::make($role), 200);
    }

<<<<<<< Updated upstream
    public function update(RoleRequest $request, Role $role)
=======
    /**
     * @OA\Patch(
     *     path="/v1/roles/{rol}",
     *     summary="Actualizar un rol",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="rol",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=100, example="Supervisor")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Supervisor")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ya existe ese rol."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="Ya existe ese rol.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Role].")
     *         )
     *     )
     * )
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, int $rol)
>>>>>>> Stashed changes
    {
        $data = $request->validated();
        $role->update($data);

        return response()->json(RoleResource::make($role), 200);
    }

<<<<<<< Updated upstream
    public function destroy(Role $role)
=======
    /**
     * @OA\Delete(
     *     path="/v1/roles/{rol}",
     *     summary="Eliminar un rol",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="rol",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Rol eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Role].")
     *         )
     *     )
     * )
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $rol)
>>>>>>> Stashed changes
    {
        $role->delete();

        return response()->json([
            'message' => 'Rol eliminado correctamente'
        ], 200);
    }

<<<<<<< Updated upstream
    public function restore(int $roleId)
=======
    /**
     * @OA\Post(
     *     path="/v1/roles/{rol}/restore",
     *     summary="Restaurar un rol eliminado",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="rol",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol restaurado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ruta restaurada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rol no encontrado entre los eliminados",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El rol ingresado no existe entre los eliminados")
     *         )
     *     )
     * )
     */
    public function restore(int $rol)
>>>>>>> Stashed changes
    {
        try {
            $roleToRestore = Role::onlyTrashed()->findOrFail($roleId);
            $this->authorize('restore', $roleToRestore);

            $roleToRestore->restore();

            return response()->json([
                'message' => 'Rol restaurado correctamente'
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'El rol ingresado no existe entre los eliminados'
            ], 404);
        }
    }
}