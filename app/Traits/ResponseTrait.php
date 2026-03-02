<?php

namespace App\Traits;

trait ResponseTrait
{
    private function errorResponse(string $message): string
    {
        return json_encode(['responseCode' => 0, 'msg' => $message]);
    }

    private function successResponse(string $message): string
    {
        return json_encode(['responseCode' => 1, 'msg' => $message]);
    }

    private function successHtmlResponse(string $html): string
    {
        return json_encode(['responseCode' => 1, 'html' => $html]);
    }
}
