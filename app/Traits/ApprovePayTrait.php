<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Http;
use App\Helpers\Helpers;
use App\Models\Contract;
use App\Models\Gateway;
use \PHPQRCode\QRcode;

trait ApprovePayTrait
{
    private string $token;
    private string $baseUrl;

    public function defineVarsApprovePay()
    {
        $gateway = Gateway::where('name', 'ApprovePay-XSA')->first();
        $this->baseUrl = $gateway->url;
        $this->token = $gateway->token;
    }

    public function createPixApprovePay(string $nsu, string $value, Contract $user, string $domain = null)
    {
        try {
            if ($value <= 0) {
                throw new Exception('O valor não é válido.');
            }

            $payload = [
                "method" => "pix",
                "order_id" => $nsu, //NUS
                "user_id" => $user->id,
                "user_name" => $domain ?? $user->owner,
                "user_document" => $user->cpf_cnpj,
                "user_address" => "--",
                "user_district" => "--",
                "user_city" => "--",
                "user_uf" => "--",
                "user_cep" => "--",
                "amount" => $value
            ];

            $url = $this->baseUrl . "/deposit";

            $response = Http::withHeaders([
                "Token" => $this->token,
                "Content-Type" => "application/json",
            ])->post($url, $payload);

            $response = json_decode($response->body());

            if (!isset($response->order_id)) {
                return ['status' => false, 'error' => $response];
            }

            $response->qr_code_b64 = $this->convertQrcodeToImage($response->content_qr);

            return ['status' => true, 'data' => $response, 'statusCode' => 200];
        } catch (\Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function verifyPixApprovePay($transactionId)
    {
        try {
            $payload = [
                'method' => 'pix',
                'order_id' => $transactionId
            ];

            $params = http_build_query($payload);

            $url = "$this->baseUrl/deposit?$params";

            $response = Http::withHeaders([
                "Token" => $this->token,
                "Content-Type" => "application/json",
            ])->get($url);

            $response = json_decode($response->body());

            if (!isset($response->orders)) {
                throw new Exception('Não foi possível verificar o pagamento.');
            }

            $response = $response->orders[0];

            return ['status' => true, 'data' => $response, 'statusCode' => 200];
        } catch (\Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function setupWebHookApprovePay($url)
    {
        try {

            $payload = ['url' => $url];

            $url = "$this->baseUrl/webhook";

            $response = Http::withHeaders([
                "Token" => $this->token,
                "Content-Type" => "application/json",
            ])->patch($url, $payload);

            $response = json_decode($response->body());

            if (!is_array($response) || !isset($response['success']) || !$response['success']) {
                throw new Exception('Nenhuma informação disponível.');
            }

            $data = ['message' => 'WebHook configurado com sucesso.'];

            return ['status' => true, 'data' => $data, 'statusCode' => 200];
        } catch (Exception $error) {
            return [
                'status' => false,
                'message' => 'Não foi possível configurar o WebHook. Visualise o console para mais detalhes.',
                'error' => $error->getMessage()
            ];
        }
    }

    private function convertQrcodeToImage($qrContent)
    {
        ob_start();
        QRcode::png($qrContent, null, 'M', 5);
        $qrCodeB64 = "data:image/png;base64," . base64_encode(ob_get_contents());
        ob_end_clean();

        return $qrCodeB64;
    }
}
