<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Http;

use App\Enum\DiscordChannelsEnum;

trait DiscordTrait
{
    private string $token;
    private string $baseUrl;

    public function defineVarsDiscord()
    {
        $secret = env('DISCORD_SECRET');
        $date = date('YmdH');

        $this->token = md5("$secret$date");
        $this->baseUrl = env('DISCORD_URL');
    }

    public function sendDiscordMessage($title, $content, $channels)
    {
        try {
            $url = $this->baseUrl . "/message";

            $body = [
                "color" => "Blue",
                "title" => $title,
                "authorName" => "Logame Manager",
                "authorImage" => "https://logame-manager.officecom.app/assets/images/logo.png",
                "content" => $content,
                "channels" => $channels
            ];

            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                'Authorization' => "Basic " . $this->token
            ])->post($url, $body);

            $response = json_decode($response->body());

            if (!isset($response) || !$response->status != 200) {
                throw new Exception("Ocorreu um problam ao tentar enviar mensagem no discord", 400);
            }

            return ['status' => true, 'data' => $response, 'statusCode' => 200];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }
}
