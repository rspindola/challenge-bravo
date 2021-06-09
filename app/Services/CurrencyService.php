<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class CurrencyService
{
    /**
     * GuzzleHttp\Client Instance
     *
     * @var Client
     */
    private $client;

    /**
     * Chave da API
     *
     * @var string
     */
    private $appKey;

    /**
     * Constructor
     */
    function __construct()
    {
        $baseUrl = env('API_URL');
        $this->appKey = env('API_KEY');

        // Configurando cliente
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout'  => 3.0
        ]);
    }

    /**
     * Faz a validação dos campos passados para converter uma moeda
     *
     * @param array $data
     * @return array
     */
    public static function validateConvertFields(?array $data, $availableCurrencies)
    {
        $errors = [];

        if (!isset($data['from'])) {
            $errors[] = 'O campo "from" é obrigatório.';
        } else if (!in_array($data['from'], $availableCurrencies)) {
            $errors[] = 'A moeda ' . $data['from'] . ' não é suportada.';
        }

        // Validação do campo to
        if (!isset($data['to'])) {
            $errors[] = 'O campo "to" é obrigatório.';
        } else if (!in_array($data['to'], $availableCurrencies)) {
            $errors[] = 'A moeda ' . $data['to'] . ' não é suportada.';
        }

        // Validação do campo amount
        if (!isset($data['amount'])) {
            $errors[] = 'O campo "amount" é obrigatório.';
        } else if (!is_numeric($data['amount'])) {
            $errors[] = 'O campo "amount" deve ser um número válido (use . como separador de decimais).';
        }

        return $errors;
    }

    /**
     * Faz a validação dos campos passados para adicionar uma moeda
     *
     * @param array $data
     * @return void
     */
    public static function validateAddFields(?array $data)
    {
        $errors = [];
        if (!isset($data['name'])) {
            $errors[] = 'O campo "currency" é obrigatório.';
        }

        if (!isset($data['usd_value'])) {
            $errors[] = 'O campo "usd_value" é obrigatório.';
        } else if (!is_numeric($data['usd_value'])) {
            $errors[] = 'O campo "usd_value" deve ser um número válido (use . como separador de decimais).';
        }

        return $errors;
    }

    /**
     * Obtém as taxas de câmbio mais recentes (usando como base o USD)
     *
     * @return array
     */
    public function getExchangeApiRates(): array
    {
        try {
            $response = $this->client->request('GET', 'latest', ['query' => [
                'apikey' => $this->appKey
            ]]);
        } catch (\Exception $e) {
            print_r($e);
            throw new Exception(json_encode(['message' => 'Erro na requisão']), 400);
        }

        return [
            'status' => !isset($e),
            'code' => isset($e) ? $e->getCode() : $response->getStatusCode(),
            'content' => isset($e) ? $e->getMessage() : (string)$response->getBody()
        ];
    }
}
