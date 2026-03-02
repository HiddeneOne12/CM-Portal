<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XssMiddleware
{
    /**
     * Patterns that indicate potential XSS (scripting). Remove or neutralize them.
     */
    protected array $dangerousPatterns = [
        '/javascript\s*:/i',
        '/vbscript\s*:/i',
        '/data\s*:/i',
        '/on\w+\s*=\s*["\']?[^"\']*["\']?/i',  // onclick=, onerror=, etc.
        '/on\w+\s*=\s*[^\s>]*/i',
        '/<script\b[^>]*>.*?<\/script>/is',
        '/<iframe\b[^>]*>.*?<\/iframe>/is',
        '/<object\b[^>]*>.*?<\/object>/is',
        '/<embed\b[^>]*>/i',
        '/<link\b[^>]*>/i',
        '/<meta\b[^>]*>/i',
        '/<style\b[^>]*>.*?<\/style>/is',
        '/expression\s*\(/i',
        '/url\s*\(/i',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $request->query->replace($this->sanitizeArray($request->query->all()));

        $input = $request->request->all();
        $request->request->replace($this->sanitizeArray($input));

        return $next($request);
    }

    /**
     * Recursively sanitize an array of input (skip files and non-scalars that shouldn't be stripped).
     */
    protected function sanitizeArray(array $data): array
    {
        $out = [];
        foreach ($data as $key => $value) {
            $out[$this->sanitizeString((string) $key)] = $this->sanitizeValue($value);
        }
        return $out;
    }

    /**
     * Sanitize a single value: string → strip XSS; array → recurse; file/other → leave as is.
     */
    protected function sanitizeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return $this->sanitizeArray($value);
        }
        if (is_string($value)) {
            return $this->sanitizeString($value);
        }
        return $value;
    }

    /**
     * Remove scripting and dangerous content from a string.
     */
    protected function sanitizeString(string $value): string
    {
        $value = strip_tags($value);
        foreach ($this->dangerousPatterns as $pattern) {
            $value = preg_replace($pattern, '', $value);
        }
        $value = preg_replace('/<\s*\/?\s*\w+[^>]*>/i', '', $value);
        return trim($value);
    }
}
