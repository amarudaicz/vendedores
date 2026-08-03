# Deployment de Seller Panel - Guía Completa

## 🚀 Sistema de Deployment Automatizado

Este sistema te permite desplegar tu aplicación Angular sin downtime y de forma segura.

## 📋 Requisitos

- Acceso a tu servidor (FTP/SFTP o acceso directo)
- PHP 7.4+ en el servidor
- Node.js y npm en tu máquina local
- Git Bash o WSL en Windows para ejecutar scripts

## 🔧 Configuración Inicial

### 1. Configurar el Script de Deployment

1. **Sube el archivo** `deploy-sellers.php` a tu servidor (directorio raíz)
2. **Edita la contraseña** en el servidor:
   ```php
   $DEPLOY_PASSWORD = 'TU_PASSWORD_AQUI'; // Cámbialo por algo seguro
   ```
3. **Verifica permisos** en el servidor:
   ```bash
   chmod 755 deploy-sellers.php
   mkdir -p backups deploy-zip
   chmod 755 backups deploy-zip
   ```

## 📦 Proceso de Deployment

### Paso 1: Compilar Localmente

Opción A: Usar el script completo (recomendado):
```bash
cd public/pages/sellers/app
./build-and-package.sh
```

Opción B: Usar el script simplificado:
```bash
cd public/pages/sellers/app
./build.sh
cd dist/browser
zip -r ../../sellers-deploy.zip .
```

### Paso 2: Subir al Servidor

Abre en tu navegador:
```
https://tu-dominio.com/deploy-sellers.php
```

O desde FileZilla:
- Sube `sellers-deploy.zip` al servidor
- Accede a `deploy-sellers.php` vía navegador

### Paso 3: Hacer el Deployment

1. Ingresa la contraseña de deployment
2. Arrastra o selecciona el archivo `sellers-deploy.zip`
3. Haz clic en "Subir y Deploy"

## ✨ Características

### ✅ Backup Automático
- Cada deployment crea un backup del estado anterior
- Se mantienen los últimos 5 backups
- Los backups se guardan en `/backups/`

### ✅ Downtime Mínimo
- Usa `rsync` para transferir solo archivos modificados
- El downtime es de segundos, no minutos
- Si hay error, puedes restaurar el backup anterior

### ✅ Interfaz Web Amigable
- Interfaz visual con logs en tiempo real
- Indicadores de progreso
- Lista de backups disponibles para restaurar

## 🔄 Restaurar un Backup

1. Accede a `deploy-sellers.php`
2. Ve a la sección "Backups Recientes"
3. Haz clic en "Restaurar" del backup deseado
4. Ingresa tu contraseña de deployment

## 🛠️ Solución de Problemas

### Error: "rsync: command not found"
El script usa `cp` como fallback automáticamente. No requiere acción.

### Error: "Error al crear backup"
Verifica permisos del directorio `/backups/`:
```bash
chmod 755 backups/
```

### Error: "No se pudo guardar el archivo"
Verifica permisos del directorio `/public/pages/sellers/app/`:
```bash
chmod 755 public/pages/sellers/app/
```

## 🔒 Seguridad

- **Cambia la contraseña** en `deploy-sellers.php` por algo seguro
- **Usa HTTPS** en producción
- **Borra el archivo** `deploy-sellers.php` si no lo usas
- **Limita el acceso** por IP si es necesario

## 📊 Flujo de Deployment

```
1. npm run build
   ↓
2. Crear ZIP de dist/browser
   ↓
3. Subir ZIP al servidor
   ↓
4. Crear backup actual
   ↓
5. Extraer ZIP en directorio temporal
   ↓
6. Copiar archivos a producción (rsync/cp)
   ↓
7. Limpiar archivos temporales
   ↓
8. ✅ Deployment completado
```

## 🎯 Beneficios vs FileZilla Manual

| FileZilla Manual | Sistema Automatizado |
|----------------|---------------------|
| Borras todo manualmente | Backup automático |
| Subes todo cada vez | Solo archivos modificados |
| Downtime de minutos | Downtime de segundos |
| Sin rollback fácil | Restore en 1 clic |
| Engorroso y lento | Rápido y seguro |

## 💡 Tips Adicionales

1. **Testea primero**: Despliega en un servidor de staging antes de producción
2. **Monitorea logs**: La interfaz muestra logs en tiempo real
3. **Usa versiones**: Considera añadir número de versión a los ZIPs
4. **Automatiza**: Crea un script que compile y abra el browser automáticamente

## 📞 Soporte

Si tienes problemas:
1. Verifica los permisos de los directorios
2. Revisa los logs del servidor PHP
3. Verifica que `rsync` o `cp` estén disponibles
4. Asegúrate de que el ZIP sea válido

---

**Notas:**
- El directorio `/backups/` puede crecer. Mínimo revisa periódicamente
- Los archivos temporales se limpian automáticamente
- El deployment es atómico: o todo funciona o nada se modifica
