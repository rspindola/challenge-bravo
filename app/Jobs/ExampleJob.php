<?php

namespace App\Jobs;

use App\Repositories\CurrencyRepository;
use App\Services\CurrencyService;

class ExampleJob extends Job
{

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $currencyService = new CurrencyService();
        $currencyRepository = new CurrencyRepository();

        $response = $currencyService->getExchangeApiRates();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
