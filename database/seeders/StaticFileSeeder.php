<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class StaticFileSeeder extends Seeder
{
    public function run(): void
    {
        $this->copyStaticFilesToPublic();
    }

    private function copyStaticFilesToPublic()
    {
        $source = base_path('data');
        $destination = storage_path('app/public');

        try {
            if (File::exists($source)) {
                File::copyDirectory($source, $destination);
                Log::info('Static files copied successfully from data to storage/app/public');
            } else {
                Log::warning('Source directory data does not exist');
            }
        } catch (\Exception $e) {
            Log::error('Failed to copy static files: ' . $e->getMessage());
        }
    }
}
