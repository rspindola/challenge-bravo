<?php

namespace App\Http\Controllers;

use App\Repositories\CurrencyRepository;
use App\Services\CurrencyService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    private $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     *
     */
    public function __construct(CurrencyRepository $repository)
    {
        $this->repository = $repository;
        // $this->middleware('auth');
    }

    /**
     * Converte a moeda de acordo com os parâmetros passados
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/currencies",
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         description="Dados do formulário",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="Dados do formulário",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         description="Dados do formulário",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Retorna o valor total da conversão",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Retorna o erro da operação.",
     *     ),
     * )
     */
    public function convertCurrency(Request $request): JsonResponse
    {
        // Obtendo as cotações no repositório
        $currencies = $this->repository->getCurrencies();

        // Obtendo os query params
        $data = $request->query();

        // Transformando as cotações em um array que eu possa buscar pelo simbolo da cotação
        $currencies = array_reduce($currencies, function ($result, $currency) {
            $result[$currency['name']] = $currency['usd_value'];
            return $result;
        });

        // Validando dados
        $errors = CurrencyService::validateConvertFields($data, array_keys($currencies));

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        // Organizando em variaveis os valores
        $usdFromValue = $currencies[$data['from']];
        $usdToValue = $currencies[$data['to']];

        // Obtendo total da conversão
        $total = ($usdToValue / $usdFromValue) * $data['amount'];

        // retornando
        return response()->json(['success' => ['total' => $total]], 200);
    }

    /**
     * Adiciona uma moeda no banco
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/currencies",
     *     @OA\Parameter(
     *         name="request",
     *         in="path",
     *         description="Dados do formulário",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Retorna o valor total da conversão",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Retorna o erro da operação.",
     *     ),
     * )
     */
    public function addCurrency(Request $request): JsonResponse
    {
        try {
            // Obtendo os parametros do formulario
            $data = $request->only('name', 'usd_value');

            // Registrando nova moeda no banco
            $result = $this->repository->saveCurrency($data);

            // retornando sucesso
            return response()->json($result);
        } catch (Exception $exception) {
            // retornando erro
            return response()->json(['errors' => ['main' => json_decode($exception->getMessage())]], $exception->getCode());
        }
    }

    /**
     * Remove uma moeda do banco
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function removeCurrency($currency): JsonResponse
    {
        try {
            // removendo a moeda do banco
            $result = $this->repository->removeCurrency($currency);

            // retornando sucesso
            return response()->json($result);
        } catch (\Exception $exception) {

            // retornando erro
            return response()->json(['errors' => ['main' => $exception->getMessage()]], $exception->getCode());
        }
    }
}
