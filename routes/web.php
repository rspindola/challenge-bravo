<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**
 * Documentação da API
 */
// $router->get('/', function () use ($router) {
//     return "Api Hurb - Challenge Bravo";
// });

$router->get(
    '/',
    ['as' => 'update', 'uses' => 'CurrencyController@updateTaxas']
);


// API route group
$router->group(['prefix' => 'api'], function () use ($router) {
    /**
     * Login ENDPOINT
     *
     * @url /login
     * @method POST
     * @param string email
     * @param string password
     */
    $router->post(
        '/auth/login',
        ['as' => 'authenticate', 'uses' => 'AuthController@postAuthenticate']
    );

    /**
     * Converter Moeda - Endpoint
     *
     * @url /currencies
     * @method GET
     * @queryParam string from
     * @queryParam string to
     * @queryParam float amount
     */
    $router->get(
        '/currencies',
        ['as' => 'currencies', 'uses' => 'CurrencyController@convertCurrency']
    );

    /**
     * Adicionar Moeda - Endpoint
     *
     * @url /currencies
     * @method POST
     * @bodyParam string currency
     * @bodyParam float usd_value
     */
    $router->post(
        '/currencies',
        ['as' => 'add-currency', 'uses' => 'CurrencyController@addCurrency']
    );

    /**
     * Remover Moeda - Endpoint
     *
     * @url /currencies/{currency}
     * @method DELETE
     */
    $router->delete(
        '/currencies/{currency}',
        ['as' => 'remove-currency', 'uses' => 'CurrencyController@removeCurrency']
    );
});
