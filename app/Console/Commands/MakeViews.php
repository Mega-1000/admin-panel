<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class MakeViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        // read --modelName
        $modelName = $this->ask('What is the model name?');

        $fileText = `@extends('layouts.datatable')
            <script src="https://cdn.tailwindcss.com" ></script>
            <script src="/js/helpers/show-hidden.js"></script>

            @section('table')
                <form action="{{ route('newsletter.store') }}" method="POST">
                    @csrf

                    Kategoria
                    <input type="text" class="form-control" name="category">

                    <br>

                    Symbol produktu
                    <input type="text" class="form-control" name="product">

                    Url aukcji
                    <input type="text" class="form-control" name="auction_url">
                    <br>

                    Opis
                    <textarea type="text" class="form-control" name="description"></textarea>
                    <br>

                    <button class="btn btn-primary">
                        Zapisz
                    </button>
                </form>
            @endsection
            `;

        $modelName = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $modelName));
        $this->info('Creating files...');
        $this->info('Creating index.blade.php...');

        if (!is_dir(resource_path("views/{$modelName}"))) {
            mkdir(resource_path("views/{$modelName}"));
        }

        // create fi
        file_put_contents(resource_path("views/{$modelName}/index.blade.php"), $fileText);
        $this->info('Creating create.blade.php...');
        file_put_contents(resource_path("views/{$modelName}/create.blade.php"), $fileText);
        $this->info('Creating edit.blade.php...');
        file_put_contents(resource_path("views/{$modelName}/edit.blade.php"), $fileText);
        $this->info('Creating show.blade.php...');
        file_put_contents(resource_path("views/{$modelName}/show.blade.php"), $fileText);

        return CommandAlias::SUCCESS;
    }
}
