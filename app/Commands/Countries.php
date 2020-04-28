<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Countries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'countries {country?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A summary of new and total cases per country.';

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
            $country  = $this->argument('country');
            $url      = 'https://api.covid19api.com/summary';
            $response = Http::get($url);
            $data     = $response->json();
            $headers  = ['Country', 'New Confirmed', 'Total Confirmed', 'New Deaths', 'Total Deaths', 'New Recovered', 'Total Recovered'];
            $items    = [];
            if ($country) {
                $countries = collect($data['Countries']);
                $items     = $countries->where('Country', $country);

                if (!$items->count()) {
                    $items = $countries->where('CountryCode', strtoupper($country));

                    if (!$items->count()) {
                        $items = $countries->where('Slug', strtolower($country));

                        if (!$items->count()) {
                            return $this->error('Country not found!');
                        }
                    }
                }
            }

            $items = $items ? $items : collect($data['Countries']);
            $items = $items->map(function ($item, $key) {
                return collect($item)->only(['Country', 'NewConfirmed', 'TotalConfirmed', 'NewDeaths', 'TotalDeaths', 'NewRecovered', 'TotalRecovered'])
                    ->map(function ($item, $key) {
                        if (in_array($key, ['NewConfirmed', 'TotalConfirmed', 'NewDeaths', 'TotalDeaths', 'NewRecovered', 'TotalRecovered'])) {
                            return number_format($item);
                        }

                        return $item;
                    });
            });

            $this->table($headers, $items->toArray());
        } catch (\Exception $e) {
            dd($e);
            $this->error($e->getMessage());
        }
    }
}
