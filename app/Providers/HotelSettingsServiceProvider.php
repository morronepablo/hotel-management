<?php

namespace App\Providers;

use App\Models\HotelSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class HotelSettingsServiceProvider extends ServiceProvider
{
    protected $defer = true; // Indica que este provider se cargará bajo demanda

    public function register()
    {
        // Registrar un singleton para las configuraciones del hotel
        $this->app->singleton('hotel.settings', function () {
            // Valores por defecto
            $nombre = 'Hotel';
            $logo = 'vendor/adminlte/dist/img/AdminLTELogo.png';

            // Verificar si la tabla existe antes de hacer la consulta
            if (Schema::hasTable('hotel_settings')) {
                $hotelSetting = HotelSetting::first();
                if ($hotelSetting) {
                    $nombre = $hotelSetting->nombre ?? 'Hotel';
                    $logo = $hotelSetting->logo ? 'uploads/' . $hotelSetting->logo : 'vendor/adminlte/dist/img/AdminLTELogo.png';
                }
            }

            return [
                'nombre' => $nombre,
                'logo' => $logo,
            ];
        });
    }

    public function boot()
    {
        // Obtener las configuraciones del singleton
        $settings = $this->app->make('hotel.settings');

        // Actualizar las configuraciones de AdminLTE dinámicamente
        Config::set('adminlte.title', $settings['nombre']);
        Config::set('adminlte.logo', '<b>' . $settings['nombre'] . '</b>');
        Config::set('adminlte.logo_img', $settings['logo']);
    }

    public function provides()
    {
        return ['hotel.settings'];
    }
}
