<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CountryList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Available countries.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $headers  = ['Country', 'Slug', 'ISO2'];
            $url      = 'https://api.covid19api.com/countries';
            $response = Http::get($url);
            $data     = $response->json();

            $this->table($headers, $data);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
