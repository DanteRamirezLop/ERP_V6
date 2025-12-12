<?php

namespace App\Services;

class Turnstile
{
    public function verify(string $token, ?string $ip = null): array
    {
        $secret = config('services.turnstile.secret_key');

        $data = http_build_query([
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $ip,
        ]);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n".
                             "Content-Length: " . strlen($data) . "\r\n",
                'content' => $data,
                'timeout' => 8,
            ]
        ]);

        $resp = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);

        if ($resp === false) {
            return ['success' => false, 'error-codes' => ['network-error']];
        }

        $json = json_decode($resp, true);
        return is_array($json) ? $json : ['success' => false, 'error-codes' => ['bad-json']];
    }
}
