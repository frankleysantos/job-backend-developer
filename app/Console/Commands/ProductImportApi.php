<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

class ProductImportApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command import the products of apiFake';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $request =  Request::create("/api/auth/product/fakeStore/{$this->option('id')}", 'POST');
        $response = app()->handle($request);
        $this->info($response);
    }
}
