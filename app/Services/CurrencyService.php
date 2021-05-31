<?php

namespace App\Services;

class CurrencyService
{

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
}
