<?php

namespace App\Repositories;

use App\Models\Currency;
use App\Services\CurrencyService;
use Exception;

class CurrencyRepository
{
    /**
     * Obtém as moedas cadastradas no banco de dados
     *
     * @return array
     */
    public function getCurrencies(): array
    {
        $currencies = Currency::all();
        return $currencies->toArray();
    }

    /**
     * Salva a moeda no banco de dados
     *
     * @param array $data
     * @return array
     */
    public function saveCurrency(array $data): array
    {
        // Validando dados
        $errors = CurrencyService::validateAddFields($data);
        if (!empty($errors)) {
            throw new Exception(json_encode($errors), 422);
        }

        $exists = $this->verifyCurrencyExists($data['name']);

        if ($exists) {
            throw new Exception(json_encode(['message' => 'O código da moeda já está registrado!']), 400);
        }

        Currency::create($data);

        return ['success' => ['message' => 'A moeda foi adicionada com sucesso!']];
    }

    /**
     * Remove a moeda no banco de dados
     *
     * @param string $currency
     * @return bool
     */
    public function removeCurrency($currency): array
    {
        $exists = $this->verifyCurrencyExists($currency);
        if (!$exists) {
            throw new Exception(json_encode(['message' => 'O código da moeda não está registrado!']), 400);
        }

        $currency = Currency::where('name', $currency)->first();
        $currency->delete();

        return ['success' => ['message' => 'A moeda foi removida com sucesso!']];
    }

    /**
     * Verifica se o código da moeda já está registrado
     *
     * @param string $currency
     * @return bool
     */
    public function verifyCurrencyExists(string $currency): bool
    {
        $stmt = Currency::where('name', $currency)->first();
        return (bool)$stmt;
    }
}
