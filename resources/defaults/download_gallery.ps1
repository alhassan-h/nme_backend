# PowerShell script to download gallery images based on descriptions from GallerySeeder
# Uses descriptions to generate descriptive filenames and downloads random images from picsum.photos
# Idempotent: skips download if file already exists
# Execution Script: powershell -ExecutionPolicy Bypass -File data/download_gallery.ps1
# Run Script: .\download_gallery.ps1

# Function to slugify description for filename
function Get-Slug {
    param([string]$text)
    $slug = $text.ToLower() -replace '[^a-z0-9\s-]', '' -replace '\s+', '-' -replace '-+', '-'
    return $slug.Trim('-')
}

# Ensure gallery directory exists
$galleryDir = "data/images/gallery"
if (!(Test-Path $galleryDir)) {
    New-Item -ItemType Directory -Force -Path $galleryDir | Out-Null
    Write-Host "Created directory: $galleryDir"
}

# Gallery image descriptions from GallerySeeder
$galleryDescriptions = @(
    'Gold Nuggets from Zamfara',
    'Limestone Quarry Operations',
    'Tin Ore Samples',
    'Coal Mining Site',
    'Iron Ore Deposits',
    'Barite Crystal Formation',
    'Mining Operations in Kaduna',
    'Mineral Products Display',
    'Mineral Market Activities',
    'Mining Industry Conference'
)

foreach ($description in $galleryDescriptions) {
    $slug = Get-Slug -text $description
    $fileName = "$slug.png"
    $outputPath = "$galleryDir/$fileName"

    if (Test-Path $outputPath) {
        Write-Host "Image already exists: $fileName, skipping download."
        continue
    }

    $url = "https://picsum.photos/800/600?random=$slug"

    Write-Host "Downloading $fileName for description: $description..."
    try {
        Invoke-WebRequest -Uri $url -OutFile $outputPath -ErrorAction Stop
        Write-Host "Downloaded $fileName successfully."
    } catch {
        Write-Host "Failed to download $fileName. Error: $($_.Exception.Message)"
    }
}

Write-Host "All gallery image downloads completed."