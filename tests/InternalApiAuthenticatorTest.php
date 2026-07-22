<?php

declare(strict_types=1);

namespace Corytech\PhpInternalAuth\Tests;

use Corytech\InternalAuth\InternalApiAuthenticator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Serializer\SerializerInterface;

class InternalApiAuthenticatorTest extends TestCase
{
    private InternalApiAuthenticator $authenticator;

    #[\Override]
    protected function setUp(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $this->authenticator = new InternalApiAuthenticator(
            serializer: $serializer,
            internalAuthToken: 'some-internal-auth-token',
        );

        parent::setUp();
    }

    #[DataProvider('dataAuthenticate')]
    public function testAuthenticate(Request $request, bool $isAuthFailed = false): void
    {
        try {
            $passport = $this->authenticator->authenticate($request);

            if ($isAuthFailed) {
                self::fail('Unexpected exception');
            }

            self::assertInstanceOf(SelfValidatingPassport::class, $passport);
        } catch (\Throwable $e) {
            if (!$isAuthFailed) {
                self::fail('Exception is unexpected');
            }

            self::assertInstanceOf(AuthenticationException::class, $e);
        }
    }

    public static function dataAuthenticate(): \Generator
    {
        yield 'failed' => [
            'request' => new Request(),
            'isAuthFailed' => true,
        ];

        yield 'success' => [
            'request' => new Request(
                server: [
                    'HTTP_Internal-Authorization' => 'some-internal-auth-token',
                ],
            ),
        ];
    }
}
