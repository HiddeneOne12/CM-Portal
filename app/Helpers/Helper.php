<?php

if (!function_exists('isInteger')) {
    function isInteger($id): bool
    {
        return is_numeric($id) && (int) $id == $id;
    }
}

if (!function_exists('dateFormat')) {
    function dateFormat($date, string $format): string
    {
        if (empty($date)) {
            return '';
        }
        return date($format, strtotime($date));
    }
}

if (!function_exists('validatePermissions')) {
    function validatePermissions(string $slug): bool
    {
        return \App\Traits\HasPermissionsTrait::getModulesPremissionsBySlug($slug);
    }
}

if (!function_exists('photo')) {
    function photo(string $username = ''): string
    {
        return asset('assets/media/avatars/blank.png');
    }
}

if (!function_exists('sanitizeInput')) {
    function sanitizeInput($value, string $type = 'string')
    {
        if (!is_string($value)) {
            return $value;
        }
        $value = strip_tags($value);
        return match ($type) {
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'alphanumeric' => preg_replace('/[^a-zA-Z0-9_. \-]/', '', $value),
            default => htmlspecialchars(addslashes($value), ENT_QUOTES, 'UTF-8'),
        };
    }
}



        /**
         * Upload a file based on type (pdf, image, video)
         *
         * @param  \Illuminate\Http\UploadedFile  $file
         * @param  string  $directory
         * @param  string  $type  (pdf | image | video)
         * @param  string|null  $prefix
         * @param  int|null  $maxSizeBytes
         * @return array
         */
        if (!function_exists('uploadFile')) {

            function uploadFile($file, $directory, $type, $prefix = null, $maxSizeBytes = null)
            {
                $fileTypes = [
                    'pdf' => [
                        'extensions' => ['pdf'],
                        'mimes' => ['application/pdf'],
                    ],
                    'image' => [
                        'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                        'mimes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                        ],
                    ],
                    'video' => [
                        'extensions' => ['mp4', 'webm', 'mov', 'mpeg'],
                        'mimes' => [
                            'video/mp4',
                            'video/webm',
                            'video/quicktime',
                            'video/mpeg',
                        ],
                    ],
                ];
        
                if (!isset($fileTypes[$type])) {
                    return ['error' => 'Invalid upload type.'];
                }
        
                if (!$file->isValid()) {
                    return ['error' => 'Invalid uploaded file.'];
                }
        
                $allowedExtensions = $fileTypes[$type]['extensions'];
                $allowedMimeTypes  = $fileTypes[$type]['mimes'];
        
                $maxSize = $maxSizeBytes ?? (8 * 1024 * 1024);
        
                /*
                |--------------------------------------------------------------------------
                | 1️⃣ Extension Validation (Server-Side)
                |--------------------------------------------------------------------------
                */
                $extension = strtolower($file->guessExtension()); // safer than client extension
        
                if (!in_array($extension, $allowedExtensions, true)) {
                    return ['error' => 'Invalid file extension.'];
                }
        
                /*
                |--------------------------------------------------------------------------
                | 2️⃣ MIME Validation (Server-Side)
                |--------------------------------------------------------------------------
                */
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $realMime = $finfo->file($file->getRealPath());
        
                if (!in_array($realMime, $allowedMimeTypes, true)) {
                    return ['error' => 'Invalid file type.'];
                }
        
                /*
                |--------------------------------------------------------------------------
                | 3️⃣ Magic Bytes Validation (File Signature Check)
                |--------------------------------------------------------------------------
                */
                $handle = fopen($file->getRealPath(), 'rb');
                $bytes  = fread($handle, 8);
                fclose($handle);
        
                $signatures = [
                    'pdf' => ["%PDF"],
                    'image' => [
                        "\xFF\xD8\xFF",        // JPEG
                        "\x89PNG\x0D\x0A\x1A\x0A", // PNG
                        "GIF8",                // GIF
                        "RIFF",                // WEBP (needs extra check but OK basic)
                    ],
                    'video' => [
                        "ftyp", // MP4/MOV
                    ],
                ];
        
                $validSignature = false;
        
                foreach ($signatures[$type] as $signature) {
                    if (strpos($bytes, $signature) !== false) {
                        $validSignature = true;
                        break;
                    }
                }
        
                if (!$validSignature) {
                    return ['error' => 'File content does not match expected type.'];
                }
        
                /*
                |--------------------------------------------------------------------------
                | 4️⃣ File Size Check
                |--------------------------------------------------------------------------
                */
                if ($file->getSize() > $maxSize) {
                    return ['error' => 'File size exceeds allowed limit.'];
                }
        
                /*
                |--------------------------------------------------------------------------
                | 5️⃣ Generate Safe Random Filename (No Original Name Used)
                |--------------------------------------------------------------------------
                */
                $safeName = uniqid(($prefix ? preg_replace('/[^a-zA-Z0-9]/', '', $prefix) . '_' : ''), true);
                $fileName = $safeName . '.' . $extension;
        
                $disk = \Storage::disk('public');
        
                if (!$disk->exists($directory)) {
                    $disk->makeDirectory($directory);
                }
        
                $stored = $disk->putFileAs($directory, $file, $fileName);
        
                return $stored
                    ? [
                        'filePath' => $directory . '/' . $fileName,
                        'fileName' => $fileName
                      ]
                    : ['error' => 'File upload failed.'];
            }
        }
    
if (!function_exists('encryptIdForUrl')) {
    /**
     * Encode an ID to a short URL-safe hash (for use in routes).
     */
    function encryptIdForUrl(int $id): string
    {
        $hashids = new \Hashids\Hashids(config('app.key'), 8);
        return $hashids->encode($id);
    }
}

if (!function_exists('decryptIdFromUrl')) {
    /**
     * Decode a URL hash back to an ID. Returns null on failure.
     */
    function decryptIdFromUrl(string $token): ?int
    {
        $hashids = new \Hashids\Hashids(config('app.key'), 8);
        $decoded = $hashids->decode($token);
        if (count($decoded) !== 1) {
            return null;
        }
        return (int) $decoded[0];
    }
}
