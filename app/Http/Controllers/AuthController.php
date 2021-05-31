<?php

namespace App\Http\Controllers;

use App\Repositories\AuthRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware('auth', ['only' => ['me']]);
    }

    /**
     * Autenticação do usuãrio atraves do passport.
     *
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function postAuthenticate(Request $request)
    {
        // validando os campos que vem no formulário pelo metodo padrão do lumen
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        try {
            // Obtendo os dados
            $data = $request->only(['email', 'password']);

            // tentando fazer o login
            $result = $this->repository->authenticate($data);

            // retornando sucesso
            return response()->json($result);
        } catch (AuthorizationException $exception) {

            // retornando erro
            return response()->json(['errors' => ['main' => $exception->getMessage()]], $exception->getCode());
        }
    }
}
