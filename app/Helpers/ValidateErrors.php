<?php

namespace App\Helpers;

use Exception;

class ValidateErrors
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
}
