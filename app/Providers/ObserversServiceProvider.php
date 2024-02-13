<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\Empresa;
use App\Observers\EmpresaObserver;
use App\Models\PreCP;
use App\Observers\PreCPObserver;
use App\Models\PreCPDetalle;
use App\Observers\PreCPDetalleObserver;
use App\Models\PreCPOrigenDestino;
use App\Observers\PreCPOrigenDestinoObserver;


class ObserversServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Empresa::observe(EmpresaObserver::class);
        // PreCP::observe(PreCPObserver::class);
        // PreCPDetalle::observe(PreCPDetalleObserver::class);
        // PreCPOrigenDestino::observe(PreCPOrigenDestinoObserver::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
