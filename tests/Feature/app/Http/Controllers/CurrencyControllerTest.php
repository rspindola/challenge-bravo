<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;

class CurrencyControllerTest extends TestCase
{

    use DatabaseMigrations, DatabaseTransactions, WithoutMiddleware;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require './bootstrap/app.php';
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate --seed');
    }

    /**
     * Teste enviando nenhuma data no formulario.
     *
     * @return void
     */
    public function testCurrencyShouldBeDeniedIfNotSendData()
    {
        $request = $this->post(route('add-currency'));
        $request->assertResponseStatus(422);
    }

    /**
     * Teste enviando um código que ja existe.
     *
     * @return void
     */
    public function testCurrencyShouldBeDeniedIfNotAlreadyHaveCode()
    {
        $payload = [
            'name' => 'BRL',
            'usd_value' => 4.00
        ];

        $request = $this->post(route('add-currency'), $payload);
        $request->assertResponseStatus(400);
        $request->seeJson(['errors' => ['main' => ['message' => 'O código da moeda já está registrado!']]]);
    }

    /**
     * Teste enviando string no numero com , ao inves de ..
     *
     * @return void
     */
    public function testCurrencyShouldBeDeniedIfisNotNumeric()
    {
        $payload = [
            'name' => 'UNI',
            'usd_value' => '4,00'
        ];

        $request = $this->post(route('add-currency'), $payload);
        $request->assertResponseStatus(422);
        $request->seeJson(['errors' => ['main' => ["O campo \"usd_value\" deve ser um número válido (use . como separador de decimais)."]]]);
    }

    /**
     * Teste enviando dados corretos.
     *
     * @return void
     */
    public function testCurrencyShouldCanSave()
    {
        $payload = [
            'name' => 'UNI',
            'usd_value' => 4.00
        ];

        $request = $this->post(route('add-currency'), $payload);
        $request->assertResponseStatus(200);
        $request->seeJson(['success' => ['message' => 'A moeda foi adicionada com sucesso!']]);
    }

    /**
     * Teste enviando dados corretos para conversao.
     *
     * @return void
     */
    public function testCurrencyShouldBeConverted()
    {
        $request = $this->get(route('currencies', ['from' => 'BTC', 'to' => 'EUR', 'amount' => 123.45]));
        $request->assertResponseStatus(200);
        $request->seeJsonStructure(['success']);
    }

    /**
     * Teste enviando dados incorretos para conversao.
     *
     * @return void
     */
    public function testCurrencyCannotConvertIfNotSendCorrectCode()
    {
        $request = $this->get(route('currencies', ['from' => 'ABC', 'to' => 'EUR', 'amount' => 123.45]));
        $request->assertResponseStatus(422);
        $request->seeJson(['errors' => ["A moeda ABC não é suportada."]]);
    }
}
