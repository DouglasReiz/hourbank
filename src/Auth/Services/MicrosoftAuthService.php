<?php

namespace App\Azhoras\Auth\Services;

use TheNetworg\OAuth2\Client\Provider\Azure;

class MicrosoftAuthService
{
    private Azure $provider;

    public function __construct()
    {
        $this->provider = new Azure([
            'clientId'                => $_ENV['MICROSOFT_CLIENT_ID'],
            'clientSecret'            => $_ENV['MICROSOFT_CLIENT_SECRET'],
            'redirectUri'             => $_ENV['MICROSOFT_REDIRECT_URI'],
            'tenant'                  => $_ENV['MICROSOFT_TENANT_ID'],
            'defaultEndPointVersion'  => '2.0',
        ]);
    }

    public function getAuthorizationUrl(): string
    {
        $url = $this->provider->getAuthorizationUrl([
            'scope' => ['openid', 'profile', 'email', 'User.Read'],
        ]);

        // Salva o state para validação no callback
        $_SESSION['oauth2_state'] = $this->provider->getState();

        return $url;
    }

    public function handleCallback(string $code, string $state): array
    {
        // Valida o state para evitar CSRF
        if (empty($_SESSION['oauth2_state']) || $state !== $_SESSION['oauth2_state']) {
            unset($_SESSION['oauth2_state']);
            throw new \RuntimeException("State inválido. Possível ataque CSRF.");
        }

        unset($_SESSION['oauth2_state']);

        $token      = $this->provider->getAccessToken('authorization_code', ['code' => $code]);
        $msUser     = $this->provider->get($this->provider->getRootMicrosoftGraphUri($token) . '/me', $token);

        // return [
        //     'email' => $msUser['mail'] ?? $msUser['userPrincipalName'],
        //     'name'  => $msUser['displayName'],
        // ];

        //MOCK TEMPORARIO PARA TESTE

        return [
            'email' => 'douglas@empresa.com.br',
            'name'  => 'Douglas Alves',
        ];
    }
}
