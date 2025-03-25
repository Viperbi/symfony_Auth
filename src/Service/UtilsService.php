<?php

namespace App\Service;

class UtilsService
{
    public function encodeBase64(mixed $data)
    {
        if (empty($data)) {
            throw new \Exception('Data is empty');
        }
        return base64_encode($data);
    }

    public function decodeBase64(string $data)
    {
        if (empty($data)) {
            throw new \Exception('Data is empty');
        }
        return base64_decode($data);
    }
}
