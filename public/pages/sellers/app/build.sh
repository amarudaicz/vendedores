#!/bin/bash

# Script simplificado para compilar la aplicación Angular
cd "$(dirname "$0")"

echo "🔨 Compilando aplicación Angular..."
npm run build

if [ $? -eq 0 ]; then
    echo "✅ Compilación exitosa!"
    echo "📁 Los archivos están en: dist/browser"
    echo ""
    echo "📦 Para crear el ZIP de deployment, ejecuta:"
    echo "   cd dist/browser && zip -r ../../sellers-deploy.zip ."
else
    echo "❌ Error en la compilación"
    exit 1
fi
