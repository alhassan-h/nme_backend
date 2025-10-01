<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
use Cloudinary\Transformation\Crop;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
        ]);
    }

    /**
     * Upload a file to Cloudinary
     *
     * @param string $filePath Path to the file or file data
     * @param array $options Additional upload options
     * @return array Upload result
     */
    public function upload($filePath, array $options = [])
    {
        try {
            $uploadApi = $this->cloudinary->uploadApi();
            $result = $uploadApi->upload($filePath, $options);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception('Cloudinary upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate a transformed image URL
     *
     * @param string $publicId Public ID of the uploaded image
     * @param array $transformations Array of transformations
     * @return string Transformed image URL
     */
    public function transform($publicId, array $transformations = [])
    {
        $image = $this->cloudinary->image($publicId);

        foreach ($transformations as $transformation) {
            if (isset($transformation['resize'])) {
                $image->resize(Resize::fill($transformation['resize']['width'], $transformation['resize']['height']));
            }
            if (isset($transformation['crop'])) {
                $image->crop(Crop::crop($transformation['crop']['width'], $transformation['crop']['height']));
            }
            // Add more transformations as needed
        }

        return $image->toUrl();
    }

    /**
     * Delete an image from Cloudinary
     *
     * @param string $publicId Public ID of the image
     * @return array Deletion result
     */
    public function delete($publicId)
    {
        try {
            $uploadApi = $this->cloudinary->uploadApi();
            $result = $uploadApi->destroy($publicId);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception('Cloudinary delete failed: ' . $e->getMessage());
        }
    }
}