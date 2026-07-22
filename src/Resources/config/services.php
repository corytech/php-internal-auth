<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Corytech\InternalAuth\InternalApiAuthenticator;
use Corytech\InternalAuth\InternalApiService;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()
        ->autoconfigure()
        ->autowire()
        ->set(InternalApiAuthenticator::class, InternalApiAuthenticator::class)
        ->set(InternalApiService::class, InternalApiService::class)
    ;
};
