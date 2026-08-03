# 🚀 Quick Start - Deployment de Seller Panel

## Setup Inicial (una sola vez)

### 1. En el servidor

Sube el archivo `deploy-sellers.php` y edita la contraseña:
```php
$DEPLOY_PASSWORD = 'tu_password_seguro_aqui';
```

### 2. En tu máquina local

En Git Bash o Terminal:
```bash
cd public/pages/sellers/app
chmod +x setup-deploy.sh
./setup-deploy.sh
```

## 🚀 Deploy Rápido

### Opción A: Git Bash / Linux (Recomendado)
```bash
cd public/pages/sellers/app
./build-and-package.sh
```

### Opción B: Windows PowerShell
```powershell
cd public\pages\sellers\app
.\build.ps1
```

### Opción C: Manual
```bash
cd public/pages/sellers/app
npm run build
cd dist/browser
zip -r ../../sellers-deploy.zip .
```

## 📤 Subir al Servidor

1. Abre en tu navegador: `https://tu-dominio.com/deploy-sellers.php`
2. Ingresa tu contraseña de deployment
3. Sube el archivo `sellers-deploy.zip`
4. Haz clic en "Subir y Deploy"

## ✅ Resultado

- ✅ Backup automático del estado anterior
- ✅ Deployment sin downtime
- ✅ Logs en tiempo real
- ✅ Restauración de backup en 1 clic

## 📖 Más Información

Lee `DEPLOYMENT.md` para detalles completos.
