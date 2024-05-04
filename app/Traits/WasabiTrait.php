<?php

namespace App\Traits;

use Exception;
use Carbon\Carbon;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

trait WasabiTrait
{
    private function uploadToWasabi($file, $path = "/")
    {
        try {
            $response = Storage::disk('wasabi')->put($path, $file);

            return $response;
        } catch (\Exception $error) {
            return $error->getMessage();
        }
    }

    private function getPresignedUrlWasabi($file)
    {
        try {
            /** @var \Illuminate\Filesystem\FilesystemManager $disk */
            $disk = Storage::disk('wasabi');
            return $disk->temporaryUrl(
                $file,
                Carbon::now()->addMinutes(5)
            );
        } catch (\Exception $error) {
            return $error->getMessage();
        }
    }
}
