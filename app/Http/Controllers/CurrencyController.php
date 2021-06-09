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
    public function __construct(CurrencyRepository $repository, CurrencyService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
        // $this->middleware('auth', ['except' => ['convertCurrency']]);
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
        try {
            // Obtendo os query params
            $data = $request->query();

            // Obtendo as cotações a serem convertidas no repositório
            $currencies = $this->repository->getCurrencyToFromConvert($data);

            // convertendo as cotacoes
            $result = $this->service->convertRates($currencies, $data['amount']);

            // retornando
            return response()->json(['success' => ['total' => $result]], 200);
        } catch (Exception $exception) {
            // retornando erro
            return response()->json(['errors' => ['main' => json_decode($exception->getMessage())]], $exception->getCode());
        }
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

    public function updateTaxas()
    {
        $currencies = $this->repository->getCurrencies();

        $result = $this->service->getExchangeApiRates();
        $rates = (json_decode($result['content'], true))['rates'];

        foreach ($currencies as $currency) {
            dd($currency);
            $currencySymbol = $currency->getCurrency();
            $currency->setUsdValue($rates[$currencySymbol]);
            // $this->repository->updateCurrency($currency);
        }

        dd($rates);
    }
}
