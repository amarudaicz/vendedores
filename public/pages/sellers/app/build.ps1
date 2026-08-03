# Script PowerShell para compilar y empaquetar la aplicación Angular

$ErrorActionPreference = "Stop"

Write-Host "🔨 Compilando aplicación Angular..." -ForegroundColor Cyan

try {
    # Cambiar al directorio del script
    $scriptPath = Split-Path -Parent $MyInvocation.MyCommand.Path
    Set-Location $scriptPath

    # Compilar la aplicación
    npm run build

    if ($LASTEXITCODE -ne 0) {
        Write-Host "❌ Error en la compilación" -ForegroundColor Red
        exit 1
    }

    Write-Host "✅ Compilación exitosa!" -ForegroundColor Green

    # Crear ZIP
    Write-Host "📦 Creando ZIP de la compilación..." -ForegroundColor Cyan
    $sourcePath = Join-Path $scriptPath "dist\browser"
    $zipPath = Join-Path $scriptPath "..\..\..\sellers-deploy.zip"

    # Usar Compress-Archive (PowerShell 5.0+)
    Compress-Archive -Path "$sourcePath\*" -DestinationPath $zipPath -Force

    if (Test-Path $zipPath) {
        $zipSize = (Get-Item $zipPath).Length
        $zipSizeMB = [math]::Round($zipSize / 1MB, 2)
        Write-Host "✅ ZIP creado exitosamente!" -ForegroundColor Green
        Write-Host "📁 Ubicación: $zipPath" -ForegroundColor Yellow
        Write-Host "📦 Tamaño: $zipSizeMB MB" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "🚀 Siguiente paso: Sube el archivo sellers-deploy.zip al servidor usando deploy-sellers.php" -ForegroundColor Cyan
    } else {
        Write-Host "❌ Error al crear el ZIP" -ForegroundColor Red
        exit 1
    }

} catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
