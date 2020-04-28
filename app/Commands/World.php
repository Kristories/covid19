<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class World extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'global';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A summary of new and total cases global.';

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
            $url      = 'https://api.covid19api.com/summary';
            $response = Http::get($url);
            $data     = $response->json();
            $headers  = [];
            $items    = collect($data['Global'])
                ->map(function ($item, $key) {
                    return [
                        'type'  => Str::title(str_replace('_', ' ', Str::snake($key))),
                        'value' => number_format($item),
                    ];
                })
                ->toArray();

            $this->table($headers, $items);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
