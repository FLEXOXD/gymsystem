$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.Drawing

$root = Split-Path -Parent $PSScriptRoot
$sourcePath = Join-Path $root 'public/pwa/flexgymlogo.png'

if (-not (Test-Path $sourcePath)) {
    throw "No existe el archivo base: $sourcePath"
}

function Save-IconPng {
    param(
        [System.Drawing.Bitmap]$Base,
        [int]$Size,
        [string]$OutputPath
    )

    $bmp = New-Object System.Drawing.Bitmap($Size, $Size, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
    try {
        $graphics = [System.Drawing.Graphics]::FromImage($bmp)
        try {
            $graphics.CompositingMode = [System.Drawing.Drawing2D.CompositingMode]::SourceCopy
            $graphics.Clear([System.Drawing.Color]::Transparent)
            $graphics.CompositingMode = [System.Drawing.Drawing2D.CompositingMode]::SourceOver
            $graphics.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
            $graphics.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
            $graphics.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
            $graphics.DrawImage(
                $Base,
                (New-Object System.Drawing.Rectangle(0, 0, $Size, $Size)),
                (New-Object System.Drawing.Rectangle(0, 0, $Base.Width, $Base.Height)),
                [System.Drawing.GraphicsUnit]::Pixel
            )
        } finally {
            $graphics.Dispose()
        }

        $outputDir = Split-Path -Parent $OutputPath
        if (-not (Test-Path $outputDir)) {
            New-Item -Path $outputDir -ItemType Directory -Force | Out-Null
        }

        $bmp.Save($OutputPath, [System.Drawing.Imaging.ImageFormat]::Png)
    } finally {
        $bmp.Dispose()
    }
}

function Write-FaviconIco {
    param(
        [string]$IcoPath,
        [string[]]$PngPaths
    )

    $entries = @()
    $payloads = @()
    $offset = 6 + (16 * $PngPaths.Count)

    foreach ($pngPath in $PngPaths) {
        [byte[]]$bytes = [System.IO.File]::ReadAllBytes($pngPath)
        if ($bytes.Length -lt 8 -or $bytes[0] -ne 137 -or $bytes[1] -ne 80 -or $bytes[2] -ne 78 -or $bytes[3] -ne 71) {
            throw "Archivo no PNG para ICO: $pngPath"
        }

        $size = [System.Drawing.Image]::FromFile($pngPath)
        try {
            $dim = if ($size.Width -ge 256) { 0 } else { [byte]$size.Width }
            $entry = New-Object byte[] 16
            $entry[0] = $dim
            $entry[1] = $dim
            $entry[2] = 0
            $entry[3] = 0
            $entry[4] = 1
            $entry[5] = 0
            $entry[6] = 32
            $entry[7] = 0
            [BitConverter]::GetBytes([uint32]$bytes.Length).CopyTo($entry, 8)
            [BitConverter]::GetBytes([uint32]$offset).CopyTo($entry, 12)

            $entries += ,$entry
            $payloads += ,$bytes
            $offset += $bytes.Length
        } finally {
            $size.Dispose()
        }
    }

    $header = New-Object byte[] 6
    $header[0] = 0
    $header[1] = 0
    $header[2] = 1
    $header[3] = 0
    [BitConverter]::GetBytes([uint16]$PngPaths.Count).CopyTo($header, 4)

    $stream = New-Object System.IO.MemoryStream
    try {
        $stream.Write($header, 0, $header.Length)
        foreach ($entry in $entries) {
            $stream.Write($entry, 0, $entry.Length)
        }
        foreach ($payload in $payloads) {
            $stream.Write($payload, 0, $payload.Length)
        }
        [System.IO.File]::WriteAllBytes($IcoPath, $stream.ToArray())
    } finally {
        $stream.Dispose()
    }
}

$source = New-Object System.Drawing.Bitmap($sourcePath)
try {
    $squareSize = [Math]::Min($source.Width, $source.Height)
    $cropX = [int][Math]::Floor(($source.Width - $squareSize) / 2)
    $cropY = [int][Math]::Floor(($source.Height - $squareSize) / 2)

    $square = New-Object System.Drawing.Bitmap($squareSize, $squareSize, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
    try {
        $graphics = [System.Drawing.Graphics]::FromImage($square)
        try {
            $graphics.CompositingMode = [System.Drawing.Drawing2D.CompositingMode]::SourceCopy
            $graphics.Clear([System.Drawing.Color]::Transparent)
            $graphics.CompositingMode = [System.Drawing.Drawing2D.CompositingMode]::SourceOver
            $graphics.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
            $graphics.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
            $graphics.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
            $graphics.DrawImage(
                $source,
                (New-Object System.Drawing.Rectangle(0, 0, $squareSize, $squareSize)),
                (New-Object System.Drawing.Rectangle($cropX, $cropY, $squareSize, $squareSize)),
                [System.Drawing.GraphicsUnit]::Pixel
            )
        } finally {
            $graphics.Dispose()
        }

        $brandSizes = @(16, 32, 48, 64, 72, 96, 128, 144, 152, 167, 180, 192, 256, 384, 512)
        foreach ($size in $brandSizes) {
            if ($size -eq 512) {
                Save-IconPng -Base $square -Size $size -OutputPath (Join-Path $root 'public/pwa/favicon-brand.png')
            } else {
                Save-IconPng -Base $square -Size $size -OutputPath (Join-Path $root ("public/pwa/favicon-brand-$size.png"))
            }
        }

        foreach ($size in @(16, 32, 180, 192, 512)) {
            Save-IconPng -Base $square -Size $size -OutputPath (Join-Path $root ("public/pwa/fg-favicon-$size.png"))
        }

        Save-IconPng -Base $square -Size 506 -OutputPath (Join-Path $root 'public/pwa/fg.png')
        Save-IconPng -Base $square -Size 512 -OutputPath (Join-Path $root 'public/pwa/flexgym.png')

        $maskSize = 512
        $mask = New-Object System.Drawing.Bitmap($maskSize, $maskSize, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
        try {
            $maskGraphics = [System.Drawing.Graphics]::FromImage($mask)
            try {
                $maskGraphics.CompositingMode = [System.Drawing.Drawing2D.CompositingMode]::SourceCopy
                $maskGraphics.Clear([System.Drawing.Color]::FromArgb(255, 2, 10, 8))
                $maskGraphics.CompositingMode = [System.Drawing.Drawing2D.CompositingMode]::SourceOver
                $maskGraphics.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
                $maskGraphics.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
                $maskGraphics.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
                $inset = [int][Math]::Round($maskSize * 0.08)
                $renderSize = $maskSize - ($inset * 2)
                $maskGraphics.DrawImage(
                    $square,
                    (New-Object System.Drawing.Rectangle($inset, $inset, $renderSize, $renderSize)),
                    (New-Object System.Drawing.Rectangle(0, 0, $square.Width, $square.Height)),
                    [System.Drawing.GraphicsUnit]::Pixel
                )
            } finally {
                $maskGraphics.Dispose()
            }

            $maskPngPath = Join-Path $root 'public/pwa/icon-maskable.png'
            $maskSvgPngPath = Join-Path $root 'public/pwa/icon-maskable.svg.png'
            $mask.Save($maskPngPath, [System.Drawing.Imaging.ImageFormat]::Png)
            $mask.Save($maskSvgPngPath, [System.Drawing.Imaging.ImageFormat]::Png)
        } finally {
            $mask.Dispose()
        }

        $iconMaskableSvg = "<svg xmlns='http://www.w3.org/2000/svg' width='512' height='512' viewBox='0 0 512 512'><rect width='512' height='512' fill='#020a08'/><image href='/pwa/flexgymlogo.png' x='41' y='41' width='430' height='430' preserveAspectRatio='xMidYMid meet'/></svg>"
        Set-Content -Path (Join-Path $root 'public/pwa/icon-maskable.svg') -Value $iconMaskableSvg -Encoding UTF8

        $iconSvg = "<svg xmlns='http://www.w3.org/2000/svg' width='512' height='512' viewBox='0 0 512 512'><image href='/pwa/flexgymlogo.png' x='0' y='0' width='512' height='512' preserveAspectRatio='xMidYMid meet'/></svg>"
        Set-Content -Path (Join-Path $root 'public/pwa/icon.svg') -Value $iconSvg -Encoding UTF8

        $icoPath = Join-Path $root 'public/favicon.ico'
        Write-FaviconIco -IcoPath $icoPath -PngPaths @(
            (Join-Path $root 'public/pwa/fg-favicon-16.png'),
            (Join-Path $root 'public/pwa/fg-favicon-32.png'),
            (Join-Path $root 'public/pwa/favicon-brand-48.png'),
            (Join-Path $root 'public/pwa/favicon-brand-64.png')
        )

        Write-Output "Iconos regenerados desde: $sourcePath"
    } finally {
        $square.Dispose()
    }
} finally {
    $source.Dispose()
}
