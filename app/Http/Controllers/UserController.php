<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Retorna la lista de usuarios
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $usuarios = User::select("id", "nombre_completo", "email", "activo")
            ->get();

        return response()->json([
            "estado" => "exito",
            "usuarios" => $usuarios,
        ], Response::HTTP_OK);
    }

    /**
     * Almacena un nuevo usuario
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validacion = Validator::make(
            $request->only("nombre_completo", "email", "contrasenia"),
            [
                "nombre_completo" => "required|string|min:6",
                "email" => "required|email|unique:users,email",
                "contrasenia" => "required|min:10",
            ]);

        if ($validacion->fails()) {
            return response()->json([
                "estado" => "error",
                "mensajes" => $validacion->getMessageBag(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $usuarioCreado = User::create(
            [
                "nombre_completo" => $request->nombre_completo,
                "email" => $request->email,
                "contrasenia" => Hash::make($request->contrasenia),
            ]
        );

        if (!$usuarioCreado) {
            return response()->json([
                "estado" => "error",
                "mensaje" => "No se ha podido crear el usuario",
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            "estado" => "exito",
            "mensaje" => "Usuario Creado Exitosamente",
            "usuario" => $usuarioCreado,
        ], Response::HTTP_CREATED);
    }

    /**
     * Retorna un usuario especifico
     *
     * @param  Int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $usuario = User::select("id", "nombre_completo", "email", "activo")
            ->find($id);

        return !$usuario
        ? response()->json([
            "estado" => "error",
            "mensaje" => "No se ha encontrado el usuario",
        ], Response::HTTP_NOT_FOUND)

        : response()->json([
            "estado" => "exitoso",
            "usuario" => $usuario,
        ], Response::HTTP_OK);

    }

    /**
     * Modifica la información de un usuario
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {

        $validacion = Validator::make(
            $request->only("nombre_completo", "email"),
            [
                "nombre_completo" => "required|string|min:6",
                "email" => "required|email|unique:users,email,{$id}",
            ]);

        if ($validacion->fails()) {
            return response()->json([
                "estado" => "error",
                "mensajes" => $validacion->getMessageBag(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json([
                "estado" => "error",
                "mensaje" => "No se ha encontrado el usuario",
            ], Response::HTTP_NOT_FOUND);
        }

        $usuario->nombre_completo = $request->nombre_completo;
        $usuario->email = $request->email;
        return $usuario->save()

        ? response()->json(["estado" => "exito", "mensaje" => "Se ha actualizado el usuario correctamente"], Response::HTTP_OK)

        : response()->json(["estado" => "error", "mensaje" => "No se ha podido actualizar el usuario"], Response::HTTP_INTERNAL_SERVER_ERROR);

    }

    /**
     * Elimina un usuario de los registros
     *
     * @param  Int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {

        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json([
                "estado" => "error",
                "mensaje" => "No se ha encontrado el usuario",
            ], Response::HTTP_NOT_FOUND);
        }

        return $usuario->delete() == 1

        ? response()->json([
            "estado" => "exito",
            "mensaje" => "Se ha eliminado el usuario exitosamente",
        ], Response::HTTP_OK)

        : response()->json([
            "estado" => "error",
            "mensaje" => "No se ha podido eliminar el usuario",
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Habilita/Deshabilita un usuario
     *
     * @param  Int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function disable($id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()
                ->json(
                    [
                        "estado" => "exito",
                        "mensaje" => "No se ha encontrado el usuario",
                    ],
                    Response::HTTP_NOT_FOUND
                );
        }

        $usuario->activo = $usuario->activo == 1 ? 0 : 1;
        $estadoMensaje = $usuario->activo == 1 ? "Habilitado" : "Deshabilitado";
        $usuario->save();

        return response()->json([
            "estado" => "exito",
            "mensaje" => "Se ha " . $estadoMensaje . " el usuario Exitosamente",
        ], Response::HTTP_OK);
    }

    /**
     * Habilita/Deshabilita un usuario
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validacion = Validator::make($request->only('email', 'contrasenia'), [
            "email" => "required|email",
            "contrasenia" => "required|min:10",
        ]);

        if ($validacion->fails()) {
            return response()->json([
                "estado" => "error",
                "mensajes" => $validacion->getMessageBag(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $usuario = User::select("email", "contrasenia")
            ->where("email", "=", $request->email)
            ->first();

        if (!$usuario) {
            return response()->json([
                "estado" => "error",
                "mensaje" => "El email es incorrecto",
            ], Response::HTTP_NOT_FOUND);
        }

        if (Hash::check($usuario->contrasenia, $request->contrasenia)) {
            return response()->json([
                "estado" => "error",
                "mensaje" => "La contraseña es incorrecta",
            ], Response::HTTP_BAD_REQUEST);
        }
        return response()->json([
            "estado" => "exito",
            "mensaje" => "Se ha iniciado sesión correctamente",
        ], Response::HTTP_OK);

    }

}
