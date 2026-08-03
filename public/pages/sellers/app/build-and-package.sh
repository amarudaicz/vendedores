#!/bin/bash

# Script para compilar y empaquetar la aplicación Angular para deployment

echo "🔨 Compilando aplicación Angular..."
cd "$(dirname "$0")"
npm run build

if [ $? -ne 0 ]; then
    echo "❌ Error en la compilación"
    exit 1
fi

echo "📦 Creando ZIP de la compilación..."
cd dist/browser
zip -r ../../sellers-deploy.zip .

if [ $? -eq 0 ]; then
    echo "✅ ZIP creado exitosamente: sellers-deploy.zip"
    echo "📁 Ubicación: $(cd ../.. && pwd)/sellers-deploy.zip"
    echo ""
    echo "🚀 Sube el archivo sellers-deploy.zip al servidor usando deploy.php"
else
    echo "❌ Error al crear el ZIP"
    exit 1
fi
