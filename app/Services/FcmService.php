<?php

namespace App\Services;

use App\Models\FcmToken;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected string $projectId;
    protected array $serviceAccount;

    public function __construct()
    {
        $this->projectId = config('services.fcm.project_id');

        $this->serviceAccount = json_decode(
            file_get_contents(storage_path('app/firebase-service-account.json')),
            true
        );
    }

    protected function getAccessToken(): string
    {
        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/firebase.messaging'],
            $this->serviceAccount
        );

        $token = $credentials->fetchAuthToken();

        if (!isset($token['access_token'])) {
            throw new \Exception('Failed to get FCM access token');
        }

        return $token['access_token'];
    }

    public function sendToAll(string $title, string $body): void
    {
        $tokens = FcmToken::pluck('token')->toArray();
        if (empty($tokens)) return;

        $accessToken = $this->getAccessToken();
        $client = new Client([
            'timeout' => 5,
        ]);
        
        foreach ($tokens as $token) {
            try {
                $client->post(
                    "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
                    [
                        'headers' => [
                            'Authorization' => "Bearer {$accessToken}",
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'message' => [
                                'token' => trim($token),
                                'notification' => [
                                    'title' => $title,
                                    'body' => $body,
                                ],
                            ],
                        ],
                    ]
                );
            } catch (RequestException $e) {
                Log::error('FCM Send Failed', [
                    'token' => $token,
                    'response' => optional($e->getResponse())->getBody()->getContents(),
                ]);
            }
        }
    }
}
