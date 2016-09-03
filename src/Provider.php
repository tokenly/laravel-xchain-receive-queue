<?php

namespace Tokenly\XchainReceiveQueue;

use Exception;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider {

    public function register() {
        $this->mergeConfigFrom(
            __DIR__.'/../config/xchainqueue.php', 'xchainqueue'
        );
    }


    public function boot() {

        $this->publishes([
            __DIR__.'/../config/xchainqueue.php' => config_path('xchainqueue.php'),
        ]);

        if (!$this->app->routesAreCached()) {
            $this->route();
        }
    }


    protected function route() {
        $xchain_receive_url_path = config('xchainqueue.receivePath');
        Route::post(
            $xchain_receive_url_path, '\Tokenly\XchainReceiveQueue\XchainReceiveHandler@handleXchainNotification'
        )->name('xchainqueue.receive');

    }
}
