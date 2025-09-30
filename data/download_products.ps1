# PowerShell script to download product images randomly (2-5 per product)

$products = @(
    "tin-ore",
    "coal-industrial",
    "iron-ore",
    "copper-ore",
    "zinc-concentrate",
    "lead-ore",
    "bauxite",
    "uranium-ore"
)

foreach ($product in $products) {
    $numImages = Get-Random -Minimum 2 -Maximum 6  # 2 to 5 images
    Write-Host "Downloading $numImages images for $product..."

    for ($i = 1; $i -le $numImages; $i++) {
        $fileName = "$product-$i.jpg"
        $outputPath = "data/images/products/$fileName"
        $randomSeed = Get-Random
        $url = "https://picsum.photos/800/600?random=$randomSeed"

        Write-Host "Downloading $fileName..."
        try {
            Invoke-WebRequest -Uri $url -OutFile $outputPath
            Write-Host "Downloaded $fileName"
        } catch {
            Write-Host "Failed to download $fileName"
        }
    }
}

Write-Host "All downloads completed."
