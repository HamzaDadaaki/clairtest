<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Afayar builds software that moves businesses forward.');
})->purpose('Display an inspiring Afayar line');
