<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if (class_exists(\Illuminate\Foundation\DevCommands::class)) {
    \Illuminate\Foundation\DevCommands::artisan('reverb:start --host=127.0.0.1 --port=8080', 'reverb');
}
