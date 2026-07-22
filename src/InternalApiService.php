<?php

declare(strict_types=1);

namespace Corytech\InternalAuth;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class InternalApiService
{
    public function __construct(
        #[Autowire('%env(INTERNAL_AUTH_TOKEN)%')]
        #[\SensitiveParameter]
        private string $internalAuthToken,
    ) {
    }

    public function isRequestAuthenticated(?string $headerAuthToken): bool
    {
        if (!$headerAuthToken || $headerAuthToken !== $this->internalAuthToken) {
            return false;
        }

        return true;
    }

    public function getAuthToken(): string
    {
        return $this->internalAuthToken;
    }
}
