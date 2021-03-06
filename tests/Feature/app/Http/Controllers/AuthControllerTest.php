<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthControllerTest extends TestCase
{

    use DatabaseMigrations, DatabaseTransactions;

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
    }

    /**
     * Teste nao enviando credenciais.
     *
     * @return void
     */
    public function testUserShouldBeDeniedIfNotSendCredentials()
    {
        $request = $this->post(route('authenticate'));
        $request->assertResponseStatus(422);
    }


    /**
     * Teste enviando usuario nao registrado
     *
     * @return void
     */
    public function testUserShouldBeDeniedIfNotRegistered()
    {
        $payload = [
            'email' => 'renato@email.com',
            'password' => '123456'
        ];

        $request = $this->post(route('authenticate'), $payload);
        $request->assertResponseStatus(401);
        $request->seeJson(['errors' => ['main' => 'Wrong credentials']]);
    }

    /**
     * Teste enviando senha incorreta.
     *
     * @return void
     */
    public function testUserShouldSendWrongPassword()
    {
        $user = User::factory()->create();
        $payload = [
            'email' => $user->email,
            'password' => 'INVALID'
        ];

        $request = $this->post(route('authenticate'), $payload);
        $request->assertResponseStatus(401);
        $request->seeJson(['errors' => ['main' => 'Wrong credentials']]);
    }

    /**
     * Teste enviando dados corretos.
     *
     * @return void
     */
    public function testUserCanAuthenticate()
    {
        $this->artisan('passport:install');
        $user = User::factory()->create();
        $payload = [
            'email' => $user->email,
            'password' => 'secret123'
        ];

        $request = $this->post(route('authenticate'), $payload);
        $request->assertResponseStatus(200);
        $request->seeJsonStructure(['access_token', 'expires_at']);
    }
}
