<?php

namespace App\Providers\Schenker;

use App\DTO\Schenker\ServiceDTO;
use App\DTO\Schenker\ServiceParameterDTO;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class SchenkerServiceProvider extends ServiceProvider
{

    const DTO_SERVICE_PARAMETERS = 'schenker.dto.service_parameters';
    const DTO_SERVICE = 'schenker.dto.service';

    public function register()
    {
        /** @var App $app */
        $this->app->bind(self::DTO_SERVICE_PARAMETERS, function ($app, $params) {
            return new ServiceParameterDTO(
                $params['mainParameter'] ?? null,
                $params['documentType'] ?? null,
                $params['documentNumber'] ?? null
            );
        });

        $this->app->bind(self::DTO_SERVICE, function ($app, $params) {
            return new ServiceDTO(
                $params['code'],
                $params['mainParameter'] ?? null,
                $params['documentType'] ?? null,
                $params['documentNumber'] ?? null
            );
        });
    }

}
