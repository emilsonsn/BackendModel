<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Http;

trait OsTicketTrait
{
    private string $token;
    private string $baseUrl;

    public function defineVarsOsTicket()
    {
        $secret = env('OS_TICKET_SECRET');
        $date = date('Y-m-d-H');

        $this->token = md5("$secret$date");
        $this->baseUrl = env('OS_TICKET_URL');
    }

    public function addTicketNote($ticket, $note)
    {
        try {
            $url = $this->baseUrl . "/addTicketNote";

            $body = [
                "ticketNumber" => $ticket,
                "message" => $note
            ];

            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                'Authorization' => "Basic " . $this->token
            ])->post($url, $body);

            $response = json_decode($response->body());

            if (!isset($response) || !$response->success) {
                throw new Exception("Ocorreu um problam ao tentar adicionar nota ao ticket $ticket", 400);
            }

            return ['status' => true, 'data' => $response, 'statusCode' => 200];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }
}
