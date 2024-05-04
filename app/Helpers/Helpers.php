<?php

namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;
use \Datetime;

class Helpers
{
    public static function toStr($errors)
    {
        try {
            $errorsStr = '';
            foreach ($errors->getMessages() as $key => $value) {
                $errorsStr .= "$key: " . $value[0] . " | ";
            }
            return $errorsStr;
        } catch (Exception $error) {
            throw new Exception($error);
        }
    }

    public static function getBase64FromImageUrl($imageUrl)
    {
        $client = new Client(['timeout' => 5]);

        try {
            $response = $client->get($imageUrl);

            if ($response->getStatusCode() != 200) {
                throw new Exception("Erro ao requisitar a imagem.");
            }

            $imageData = $response->getBody()->getContents();
            return base64_encode($imageData);
        } catch (Exception $error) {
            throw new Exception("Exceção: " . $error->getMessage());
        }
    }

    public static function calcNextDate($dia)
    {
        // Verifica se o dia está no intervalo válido
        if ($dia < 1 || $dia > 25) {
            $dia = 1;
        }

        $dataAtual = new DateTime();

        $diaAtual = $dataAtual->format('d');
        $mesAtual = $dataAtual->format('n');

        $anoAtual = $dataAtual->format('Y');

        if ($diaAtual > $dia) {
            $mesAtual++;
        }

        if ($mesAtual == 13) {
            $mesAtual = 1;
            $anoAtual++;
        }

        $proximaData = new DateTime("$anoAtual-$mesAtual-$dia");

        $dataFormatada = $proximaData->format('Y-m-d');

        return $dataFormatada;
    }

    public static function padZero($number)
    {
        return $number < 10 ? "0$number" : "$number";
    }

    public static function brlMoneyFormat($value)
    {
        return number_format($value, 2, ",", ".");
    }
}
