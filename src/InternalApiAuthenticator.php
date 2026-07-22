<?php

declare(strict_types=1);

namespace Corytech\InternalAuth;

use Corytech\OpenApi\DTO\CommonApiErrorCode;
use Corytech\OpenApi\DTO\ResponseError;
use Corytech\OpenApi\DTO\ResponseWrapper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Serializer\SerializerInterface;

class InternalApiAuthenticator extends AbstractAuthenticator
{
    public const string INTERNAL_AUTHORIZATION_HEADER = 'Internal-Authorization';

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly InternalApiService $service,
    ) {
    }

    #[\Override]
    public function supports(Request $request): ?bool
    {
        return true;
    }

    #[\Override]
    public function authenticate(Request $request): Passport
    {
        $headerAuthToken = $request->headers->get(self::INTERNAL_AUTHORIZATION_HEADER);
        if (!$this->service->isRequestAuthenticated($headerAuthToken)) {
            throw new AuthenticationException();
        }

        $user = new InternalApiUser($request->headers->get(self::INTERNAL_AUTHORIZATION_HEADER));

        return new SelfValidatingPassport(
            new UserBadge(
                $user->getUserIdentifier(),
                static fn (string $userIdentifier): ?UserInterface => $user
            )
        );
    }

    #[\Override]
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    #[\Override]
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            $this->serializer->serialize(
                new ResponseWrapper(null, new ResponseError(CommonApiErrorCode::AuthenticationFailed)),
                'json',
            ),
            json: true
        );
    }
}
