<?php

namespace App\Api\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        /*** @var PathItem $path */
        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
            if ($path->getGet() && $path->getGet()->getSummary() === 'hidden') {
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        }

        $schemas = $openApi->getComponents()->getSecuritySchemes();
        $schemas['BearerAuth'] = new ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT'
        ]);

        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => ['type' => 'string', 'example' => 'johndoe@example.com'],
                'password' => ['type' => 'string', 'example' => '0000']
            ]
        ]);

        $schemas['RefreshToken'] = new ArrayObject([
            'type' => 'object',
            'properties' => ['refresh_token' => ['type' => 'string', 'example' => 'Le token ici']]
        ]);

        $schemas['PasswordReset'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'email' => ['type' => 'string', 'example' => 'johndoe@example.com']
            ]
        ]);

        $schemas['SocialLogin'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'service' => ['type' => 'string', 'example' => 'google'],
                'id' => ['type' => 'string', 'example' => '11111111111111'],
                'email' => ['type' => 'string', 'example' => 'johndoe@example.com'],
                'email_verified' => ['type' => 'boolean', 'example' => 'false'],
            ]
        ]);

        $loginPathItem = new PathItem(null, null, null, null, null,
            new Operation(
                'postApiLogin',
                ['Auth'],
                [
                    '200' => [
                        'description' => 'Récupère le token JWT',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ]
                    ]
                ],
                'Récupère un jeton de connexion JWT',
                '', null,
                [],
                new RequestBody(
                    '',
                    new ArrayObject([
                        'application/json' => [
                            'schema' => ['$ref' => '#/components/schemas/Credentials']
                        ]
                    ])
                )
            )
        );

        $refreshTokenPathItem = new PathItem(null, null, null, null, null,
            new Operation(
                'postApiRefreshToken',
                ['Auth'],
                [
                    '200' => [
                        'description' => 'Rafraichit le token JWT',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Refresh',
                                ],
                            ],
                        ]
                    ],
                ],
                'Rafraichit le jeton de connexion JWT',
                '', null,
                [],
                new RequestBody(
                    '',
                    new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/RefreshToken'
                            ]
                        ]
                    ])
                )
            )
        );

        $passwordResetPathItem = new PathItem(null, null, null, null, null,
            new Operation(
                'postApiPasswordReset',
                ['Auth'],
                [
                    '204' => ['description' => 'Réinitialise le mot de passe']
                ],
                'Réinitialise le mot de passe de l\'utilisateur',
                '', null,
                [],
                new RequestBody(
                    '',
                    new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/PasswordReset'
                            ]
                        ]
                    ])
                )
            )
        );

        $socialLoginPathItem = new PathItem(null, null, null, null, null,
            new Operation(
                'postApiSocialLogin',
                ['Auth'],
                [ // Récupère un jeton de connexion JWT
                    '200' => ['description' => 'Récupère le token JWT'],
                    '404' => ['description' => 'Aucun token trouver, identifiant incorrecte']
                ],
                'Récupère un jeton de connexion JWT grace a un compte Google ou Facebook',
                '', null,
                [],
                new RequestBody(
                    '',
                    new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/SocialLogin'
                            ]
                        ]
                    ])
                )
            )
        );

        $openApi->getPaths()->addPath('/api/login', $loginPathItem);
        $openApi->getPaths()->addPath('/api/token/refresh', $refreshTokenPathItem);
        $openApi->getPaths()->addPath('/api/password-reset', $passwordResetPathItem);
        $openApi->getPaths()->addPath('/api/login/social', $socialLoginPathItem);

        return $openApi;
    }
}