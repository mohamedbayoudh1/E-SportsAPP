<?php

namespace App\Service;

use Firebase\JWT\JWT;

class FirebaseStorageService
{
    private $privateKey;

    public function __construct(string $privateKeyFile)
    {
        $this->privateKey = json_decode(file_get_contents($privateKeyFile), true);
    }

    public function getPrivateKey(): array
    {
        return $this->privateKey;
    }

    public function getAccessToken(): string
    {
        $payload = [
            "iss" => $this->privateKey['client_email'],
            "sub" => $this->privateKey['client_email'],
            "aud" => "https://oauth2.googleapis.com/token",
            "iat" => time(),
            "exp" => time() + 3600,
        ];

        return JWT::encode($payload, $this->privateKey['private_key'], "RS256");
    }
}
