<?php

declare(strict_types=1);

namespace Corytech\PhpInternalAuth\Security\InternalAuthenticator;

use Symfony\Component\Security\Core\User\UserInterface;

readonly class InternalApiUser implements UserInterface
{
    public function __construct(
        private string $token,
    ) {
    }

    #[\Override]
    public function getRoles(): array
    {
        return [];
    }

    #[\Override]
    public function getUserIdentifier(): string
    {
        return $this->token;
    }
}
