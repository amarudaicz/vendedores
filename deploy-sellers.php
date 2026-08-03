<?php
/**
 * Script de Deployment para Seller Panel
 * Sube este archivo al servidor y accede vía navegador:
 * https://tu-dominio.com/deploy-sellers.php
 */

// Configuración de seguridad (cambia esto por valores seguros)
$DEPLOY_PASSWORD = 'TU_PASSWORD_AQUI'; // Contraseña para permitir deploy
$BACKUP_DIR = __DIR__ . '/backups';
$DEPLOY_DIR = __DIR__ . '/public/pages/sellers/app';
$ZIP_UPLOAD_DIR = __DIR__ . '/deploy-zip';
$MAX_BACKUPS = 5; // Mantener últimos 5 backups

// Crear directorios necesarios
foreach ([$BACKUP_DIR, $ZIP_UPLOAD_DIR] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Funciones de utilidad
function logMessage($message) {
    echo "<div class='log " . ($message['type'] ?? 'info') . "'>{$message['text']}</div>";
    flush();
}

function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
}

// Manejar POST del archivo ZIP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] !== $DEPLOY_PASSWORD) {
        http_response_code(403);
        die('❌ Contraseña incorrecta');
    }

    if (!isset($_FILES['deploy_file'])) {
        http_response_code(400);
        die('❌ No se recibió ningún archivo');
    }

    $file = $_FILES['deploy_file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        die('❌ Error al subir el archivo: ' . $file['error']);
    }

    if ($file['type'] !== 'application/zip' && $file['type'] !== 'application/x-zip-compressed') {
        http_response_code(400);
        die('❌ El archivo debe ser un ZIP');
    }

    $zipPath = $ZIP_UPLOAD_DIR . '/deploy-' . time() . '.zip';
    if (!move_uploaded_file($file['tmp_name'], $zipPath)) {
        http_response_code(500);
        die('❌ Error al guardar el archivo');
    }

    // Procesar deployment
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Deploy Seller Panel - Procesando</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
            .log { padding: 10px; margin: 5px 0; border-radius: 4px; }
            .log.info { background: #e3f2fd; }
            .log.success { background: #c8e6c9; color: #2e7d32; }
            .log.error { background: #ffcdd2; color: #c62828; }
            .log.warning { background: #fff3cd; }
            h1 { color: #1976d2; }
            .progress { background: #e0e0e0; height: 20px; border-radius: 10px; overflow: hidden; margin: 10px 0; }
            .progress-bar { background: #4caf50; height: 100%; width: 0%; transition: width 0.3s; }
        </style>
    </head>
    <body>
        <h1>🚀 Deploy Seller Panel</h1>
        <div id="logs"></div>
        <script>
            function addLog(type, text) {
                const logsDiv = document.getElementById('logs');
                const logDiv = document.createElement('div');
                logDiv.className = 'log ' + type;
                logDiv.textContent = text;
                logsDiv.appendChild(logDiv);
                window.scrollTo(0, document.body.scrollHeight);
            }
        </script>
    <?php

    try {
        logMessage(['type' => 'info', 'text' => '📦 Archivo ZIP recibido: ' . formatBytes(filesize($zipPath))]);

        // Crear backup
        if (is_dir($DEPLOY_DIR . '/browser')) {
            $backupPath = $BACKUP_DIR . '/browser-backup-' . date('Y-m-d-H-i-s');
            logMessage(['type' => 'info', 'text' => '💾 Creando backup...']);

            // Crear backup usando exec para mejor rendimiento
            $backupCmd = sprintf('cp -r %s %s', escapeshellarg($DEPLOY_DIR . '/browser'), escapeshellarg($backupPath));
            exec($backupCmd, $output, $returnCode);

            if ($returnCode === 0) {
                logMessage(['type' => 'success', 'text' => '✅ Backup creado exitosamente']);

                // Limpiar backups antiguos
                $backups = glob($BACKUP_DIR . '/browser-backup-*');
                if (count($backups) > $MAX_BACKUPS) {
                    usort($backups, function($a, $b) {
                        return filemtime($a) - filemtime($b);
                    });
                    $toDelete = array_slice($backups, 0, count($backups) - $MAX_BACKUPS);
                    foreach ($toDelete as $oldBackup) {
                        exec('rm -rf ' . escapeshellarg($oldBackup));
                    }
                    logMessage(['type' => 'info', 'text' => '🗑️  Backups antiguos eliminados']);
                }
            } else {
                logMessage(['type' => 'error', 'text' => '❌ Error al crear backup']);
            }
        }

        // Extraer ZIP en directorio temporal
        logMessage(['type' => 'info', 'text' => '📂 Extrayendo ZIP...']);
        $tempDir = $ZIP_UPLOAD_DIR . '/temp-' . time();
        mkdir($tempDir, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($tempDir);
            $zip->close();

            logMessage(['type' => 'success', 'text' => '✅ ZIP extraído exitosamente']);

            // Copiar archivos nuevos
            logMessage(['type' => 'info', 'text' => '📋 Copiando archivos al directorio de producción...']);
            $deployTarget = $DEPLOY_DIR . '/browser';

            if (!is_dir($deployTarget)) {
                mkdir($deployTarget, 0755, true);
            }

            // Usar rsync si está disponible, sino copiar archivos
            $copyCmd = sprintf('rsync -av --delete %s/ %s/',
                escapeshellarg($tempDir),
                escapeshellarg($deployTarget)
            );
            exec($copyCmd, $output, $returnCode);

            if ($returnCode !== 0) {
                // Fallback a copia manual si rsync no funciona
                $copyCmd = sprintf('cp -r %s/* %s/',
                    escapeshellarg($tempDir),
                    escapeshellarg($deployTarget)
                );
                exec($copyCmd, $output, $returnCode);
            }

            if ($returnCode === 0) {
                logMessage(['type' => 'success', 'text' => '✅ Archivos copiados exitosamente']);
            } else {
                throw new Exception('Error al copiar archivos');
            }

            // Limpiar directorio temporal
            exec('rm -rf ' . escapeshellarg($tempDir));
            exec('rm ' . escapeshellarg($zipPath));

            logMessage(['type' => 'success', 'text' => '🎉 Deployment completado exitosamente!']);
            logMessage(['type' => 'info', 'text' => '⏱️  Tiempo total: ' . (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) . ' segundos']);
        } else {
            throw new Exception('Error al abrir el archivo ZIP');
        }

    } catch (Exception $e) {
        logMessage(['type' => 'error', 'text' => '❌ Error durante el deployment: ' . $e->getMessage()]);
    }

    ?>
        <script>
            setTimeout(() => {
                window.location.href = window.location.href;
            }, 5000);
        </script>
    </body>
    </html>
    <?php
    exit;
}

// Mostrar formulario de upload
?>
<!DOCTYPE html>
<html>
<head>
    <title>Deploy Seller Panel</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1976d2;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group { margin-bottom: 20px; }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="password"]:focus {
            border-color: #1976d2;
            outline: none;
        }
        .upload-zone {
            border: 3px dashed #ccc;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #fafafa;
        }
        .upload-zone:hover, .upload-zone.dragover {
            border-color: #1976d2;
            background: #e3f2fd;
        }
        .upload-zone input {
            display: none;
        }
        .upload-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .file-info {
            margin-top: 15px;
            padding: 10px;
            background: #e8f5e9;
            border-radius: 4px;
            display: none;
        }
        button {
            width: 100%;
            padding: 15px;
            background: #1976d2;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #1565c0;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .backups-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        .backups-section h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }
        .backup-list {
            list-style: none;
            padding: 0;
        }
        .backup-item {
            padding: 10px;
            background: #f9f9f9;
            margin-bottom: 5px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .backup-info { font-size: 14px; color: #666; }
        .backup-actions button {
            width: auto;
            padding: 5px 10px;
            font-size: 12px;
        }
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #1976d2;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Deploy Seller Panel</h1>

        <form id="deployForm" onsubmit="return false;">
            <div class="form-group">
                <label for="password">🔑 Contraseña de Deployment:</label>
                <input type="password" id="password" placeholder="Ingresa tu contraseña" required>
            </div>

            <div class="form-group">
                <label>📦 Archivo ZIP de la compilación:</label>
                <div class="upload-zone" id="uploadZone">
                    <div class="upload-icon">📁</div>
                    <p>Arrastra el archivo ZIP aquí o haz clic para seleccionar</p>
                    <p style="font-size: 12px; color: #666; margin-top: 10px;">
                        Ejecuta: <code>./build-and-package.sh</code> para crear el ZIP
                    </p>
                    <input type="file" id="fileInput" accept=".zip" required>
                </div>
                <div class="file-info" id="fileInfo"></div>
            </div>

            <button type="submit" id="deployBtn" disabled>
                <span id="btnText">Subir y Deploy</span>
                <span id="loadingSpinner" class="loading" style="display: none; margin-left: 10px;"></span>
            </button>
        </form>

        <div class="backups-section">
            <h2>💾 Backups Recientes</h2>
            <ul class="backup-list" id="backupList">
                <!-- Se llenará con JavaScript -->
            </ul>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('fileInput');
        const uploadZone = document.getElementById('uploadZone');
        const fileInfo = document.getElementById('fileInfo');
        const deployBtn = document.getElementById('deployBtn');
        const btnText = document.getElementById('btnText');
        const loadingSpinner = document.getElementById('loadingSpinner');
        let selectedFile = null;

        // Manejo de drag and drop
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        });

        uploadZone.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFile(e.target.files[0]);
            }
        });

        function handleFile(file) {
            if (file.type !== 'application/zip' && file.type !== 'application/x-zip-compressed') {
                alert('Por favor selecciona un archivo ZIP');
                return;
            }
            selectedFile = file;
            fileInfo.style.display = 'block';
            fileInfo.innerHTML = `
                <strong>Archivo seleccionado:</strong> ${file.name}<br>
                <strong>Tamaño:</strong> ${formatBytes(file.size)}
            `;
            deployBtn.disabled = false;
        }

        function formatBytes(bytes) {
            const units = ['B', 'KB', 'MB', 'GB'];
            bytes = Math.max(bytes, 0);
            const pow = Math.floor((bytes ? Math.log(bytes) : 0) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, pow) * 100) / 100 + ' ' + units[pow];
        }

        document.getElementById('deployForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const password = document.getElementById('password').value;
            if (!password || !selectedFile) {
                alert('Por favor completa todos los campos');
                return;
            }

            const formData = new FormData();
            formData.append('password', password);
            formData.append('deploy_file', selectedFile);

            deployBtn.disabled = true;
            btnText.textContent = 'Procesando...';
            loadingSpinner.style.display = 'inline-block';

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    window.location.href = window.location.href;
                } else {
                    alert('Error en el deployment: ' + response.statusText);
                    deployBtn.disabled = false;
                    btnText.textContent = 'Subir y Deploy';
                    loadingSpinner.style.display = 'none';
                }
            } catch (error) {
                alert('Error de conexión: ' + error.message);
                deployBtn.disabled = false;
                btnText.textContent = 'Subir y Deploy';
                loadingSpinner.style.display = 'none';
            }
        });

        // Cargar lista de backups
        fetchBackups();

        async function fetchBackups() {
            try {
                const response = await fetch('?action=list_backups');
                const backups = await response.json();
                const backupList = document.getElementById('backupList');

                if (backups.length === 0) {
                    backupList.innerHTML = '<li style="color: #999;">No hay backups disponibles</li>';
                    return;
                }

                backupList.innerHTML = backups.map(backup => `
                    <li class="backup-item">
                        <div class="backup-info">
                            <strong>${backup.name}</strong><br>
                            ${backup.date} - ${backup.size}
                        </div>
                        <div class="backup-actions">
                            <button onclick="restoreBackup('${backup.path}')">Restaurar</button>
                        </div>
                    </li>
                `).join('');
            } catch (error) {
                console.error('Error al cargar backups:', error);
            }
        }

        async function restoreBackup(path) {
            if (!confirm('¿Estás seguro de que quieres restaurar este backup?')) return;

            const password = document.getElementById('password').value;
            if (!password) {
                alert('Por favor ingresa la contraseña de deployment');
                return;
            }

            try {
                const response = await fetch('?action=restore', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ password, path })
                });

                if (response.ok) {
                    alert('Backup restaurado exitosamente');
                    window.location.reload();
                } else {
                    alert('Error al restaurar backup');
                }
            } catch (error) {
                alert('Error de conexión: ' + error.message);
            }
        }
    </script>
</body>
</html>
