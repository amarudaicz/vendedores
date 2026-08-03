#!/bin/bash

# Script para configurar permisos de los scripts de deployment

echo "🔧 Configurando permisos de scripts..."

# Dar permisos de ejecución a los scripts bash
chmod +x build.sh
chmod +x build-and-package.sh
chmod +x setup-deploy.sh

echo "✅ Scripts configurados correctamente!"
echo ""
echo "📋 Scripts disponibles:"
echo "  - build.sh              : Solo compila la aplicación"
echo "  - build-and-package.sh  : Compila y crea el ZIP"
echo "  - setup-deploy.sh       : Configura permisos (este script)"
echo ""
echo "📖 Para más información, lee: ../../DEPLOYMENT.md"
