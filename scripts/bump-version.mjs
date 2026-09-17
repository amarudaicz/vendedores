#!/usr/bin/env node
// scripts/bump-version.mjs
// Auto-incrementa el PATCH de version.json y genera version.ts antes de cada push.

import { readFileSync, writeFileSync } from "fs";
import { execSync } from "child_process";
import { resolve, dirname } from "path";
import { fileURLToPath } from "url";

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, "..");

// --- 1. Leer y bumping version.json ---
const versionFile = resolve(ROOT, "version.ts");

let content;

try {
  content = readFileSync(versionFile, "utf8");
} catch (err) {
  console.error(`bump: cannot read ${versionFile}: ${err.message}`);
  process.exit(1);
}

const regex = /(\d+)\.(\d+)\.(\d+)/;
const match = content.match(regex);

if (!match) {
  console.error(
    `bump: invalid version format in ${versionFile}. Expected export const version = 'X.Y.Z'`,
  );
  process.exit(1);
}

const oldVersion = `${match[1]}.${match[2]}.${match[3]}`;
let X = Number(match[1]);
let Y = Number(match[2]);
let Z = Number(match[3]);

if (Z < 10) {
  Z++;
} else {
  Z = 0;
  if (Y < 10) {
    Y++;
  } else {
    Y = 0;
    X++;
  }
}

const newVersion = `${X}.${Y}.${Z}`;
const nextContent = `export const version = '${newVersion}'\n`;

const versionTsPath = resolve(
  ROOT,
  "public/pages/sellers/app/src/app/version.ts",
);
writeFileSync(versionFile, nextContent);
writeFileSync(versionTsPath, nextContent);

// --- 4. Stage los archivos ---
execSync(`git add "${versionFile}" "${versionTsPath}"`);

console.log(`✅ Version bumped to ${newVersion}`);
