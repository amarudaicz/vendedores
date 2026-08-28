#!/usr/bin/env node
// scripts/bump-version.mjs
// Auto-incrementa el PATCH de version.json y genera version.ts en cada commit.
// Se ejecuta desde el hook pre-commit: los archivos de versión se incluyen
// automáticamente en el commit del usuario (no se crea un commit separado).

import { readFileSync, writeFileSync } from 'fs';
import { execSync } from 'child_process';
import { resolve, dirname } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, '..');

// --- 1. Leer y bumping version.json ---
const versionJsonPath = resolve(ROOT, 'version.json');
const versionData = JSON.parse(readFileSync(versionJsonPath, 'utf-8'));

const [major, minor, patch] = versionData.version.split('.').map(Number);
const newVersion = `${major}.${minor}.${patch + 1}`;
versionData.version = newVersion;

writeFileSync(versionJsonPath, JSON.stringify(versionData, null, 2) + '\n');

// --- 2. Obtener metadata de git ---
const branch = execSync('git rev-parse --abbrev-ref HEAD').toString().trim();
// HEAD aún no tiene el nuevo commit, usamos el anterior como referencia
const commit = execSync('git rev-parse --short HEAD').toString().trim();
const date = new Date().toISOString().split('T')[0];

// --- 3. Generar version.ts ---
const versionTs = `// AUTO-GENERADO — no editar manualmente
// Generado el ${new Date().toISOString()}
export const VERSION = {
  version: '${newVersion}',
  branch: '${branch}',
  commit: '${commit}',
  date: '${date}',
};
`;

const versionTsPath = resolve(ROOT, 'public/pages/sellers/app/src/app/version.ts');
writeFileSync(versionTsPath, versionTs);

// --- 4. Stage los archivos para que queden incluidos en el commit actual ---
execSync(`git add "${versionJsonPath}" "${versionTsPath}"`);

console.log(`✅ Version bumped to ${newVersion} (${branch}@${commit})`);
