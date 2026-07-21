<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Corytech\InternalAuth\InternalApiAuthenticator;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()
        ->autoconfigure()
        ->autowire()
        ->set(InternalApiAuthenticator::class, InternalApiAuthenticator::class)
    ;
};
