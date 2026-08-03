#!/usr/bin/env node
/**
 * generate-tokens.js
 *
 * Parses a DESIGN.md YAML frontmatter and generates:
 *   - tailwind.config.js  (colors, spacing, borderRadius, fontFamily, screens)
 *   - tokens.css          (CSS custom properties for typography + components)
 *
 * Usage:
 *   node generate-tokens.js path/to/DESIGN.md
 *   node generate-tokens.js path/to/DESIGN.md --out ./src/styles
 *   node generate-tokens.js path/to/DESIGN.md --merge ./tailwind.config.js
 */

const fs = require('fs');
const path = require('path');

// ── CLI args ────────────────────────────────────────────────────────────────
const args = process.argv.slice(2);
const mdPath = args.find(a => !a.startsWith('--'));
const outFlag = args.indexOf('--out');
const outDir = outFlag !== -1 ? args[outFlag + 1] : '.';
const mergeFlag = args.indexOf('--merge');

if (!mdPath) {
  console.error('Usage: node generate-tokens.js path/to/DESIGN.md [--out ./dir] [--merge ./tailwind.config.js]');
  process.exit(1);
}

// ── YAML Parser (lightweight, handles DESIGN.md structure) ─────────────────
function parseYamlValue(raw) {
  const v = raw.trim();
  if (v === 'null' || v === '~') return null;
  if (v === 'true') return true;
  if (v === 'false') return false;
  // Quoted string
  if ((v.startsWith("'") && v.endsWith("'")) || (v.startsWith('"') && v.endsWith('"'))) {
    return v.slice(1, -1);
  }
  // Number
  if (/^-?\d+(\.\d+)?$/.test(v)) return Number(v);
  if (/^-?\.\d+$/.test(v)) return Number(v);
  // Array  [item1, item2, ...]
  if (v.startsWith('[') && v.endsWith(']')) {
    const inner = v.slice(1, -1);
    if (inner.trim() === '') return [];
    return inner.split(',').map(s => parseYamlValue(s.trim()));
  }
  return v; // keep as string (e.g. "80px", "#da291c")
}

function parseYaml(yamlText) {
  const lines = yamlText.split('\n');
  const root = {};
  // stack entries: { indent, obj, array? }
  const stack = [{ indent: -1, obj: root, array: null }];
  // multi-line string state
  let mlKey = null;
  let mlIndent = -1;
  let mlLines = [];

  function flushMl() {
    if (mlKey) {
      const parent = stack[stack.length - 1].obj;
      parent[mlKey] = mlLines.join('\n');
      mlKey = null;
      mlLines = [];
    }
  }

  for (let i = 0; i < lines.length; i++) {
    const line = lines[i];
    if (line.trim() === '' || line.trim().startsWith('#')) {
      if (mlKey) continue;
      continue;
    }

    const indent = line.search(/\S/);
    const trimmed = line.trim();
    const parent = stack[stack.length - 1];

    // If we were collecting multi-line string
    if (mlKey) {
      if (indent > mlIndent) {
        mlLines.push(trimmed);
        continue;
      } else {
        flushMl();
      }
    }

    // Array item: - value
    if (trimmed.startsWith('- ')) {
      const val = parseYamlValue(trimmed.slice(2));
      if (!parent.array) {
        parent.array = [];
        parent.obj[parent._lastKey] = parent.array;
      }
      parent.array.push(val);
      continue;
    }

    // Key: value
    const colonIdx = trimmed.indexOf(':');
    if (colonIdx === -1) {
      // Could be a bare scalar under a key — skip
      continue;
    }

    const key = trimmed.slice(0, colonIdx).trim();
    const rest = trimmed.slice(colonIdx + 1).trim();

    // Pop stack to correct indent
    while (stack.length > 1 && stack[stack.length - 1].indent >= indent) {
      stack.pop();
    }

    const currentParent = stack[stack.length - 1].obj;
    currentParent._lastKey = key;

    if (rest === '') {
      // Start new nested object
      const newObj = {};
      currentParent[key] = newObj;
      stack.push({ indent, obj: newObj, array: null });
    } else if (rest === '|') {
      // Multi-line string starts on next line
      mlKey = key;
      mlIndent = indent;
      mlLines = [];
    } else {
      currentParent[key] = parseYamlValue(rest);
    }
  }

  flushMl();

  // Clean up internal keys
  function clean(obj) {
    if (obj && typeof obj === 'object' && !Array.isArray(obj)) {
      delete obj._lastKey;
      for (const k of Object.keys(obj)) clean(obj[k]);
    }
  }
  clean(root);

  return root;
}

// ── Token Reference Resolution ─────────────────────────────────────────────
function resolveRefs(obj, root, path = '') {
  if (typeof obj === 'string') {
    const match = obj.match(/^\{([a-zA-Z0-9_.-]+)\}$/);
    if (match) {
      const parts = match[1].split('.');
      let resolved = root;
      for (const p of parts) {
        if (resolved && typeof resolved === 'object' && p in resolved) {
          resolved = resolved[p];
        } else {
          return obj; // can't resolve, keep original
        }
      }
      // If resolved to an object, deep clone
      if (resolved && typeof resolved === 'object' && !Array.isArray(resolved)) {
        return JSON.parse(JSON.stringify(resolved));
      }
      return resolved;
    }
    return obj;
  }
  if (Array.isArray(obj)) {
    return obj.map(item => resolveRefs(item, root, path));
  }
  if (obj && typeof obj === 'object') {
    const result = {};
    for (const key of Object.keys(obj)) {
      result[key] = resolveRefs(obj[key], root, `${path}.${key}`);
    }
    return result;
  }
  return obj;
}

// ── Read & parse DESIGN.md ─────────────────────────────────────────────────
const content = fs.readFileSync(mdPath, 'utf-8');

const fmMatch = content.match(/^---\s*\n([\s\S]*?)\n---/);
if (!fmMatch) {
  console.error('No YAML frontmatter found in', mdPath);
  process.exit(1);
}

const rawYaml = fmMatch[1];
const parsed = parseYaml(rawYaml);
const resolved = resolveRefs(parsed, parsed);

const colors = resolved.colors || {};
const typography = resolved.typography || {};
const spacing = resolved.spacing || {};
const rounded = resolved.rounded || {};
const components = resolved.components || {};
const name = resolved.name || 'design-system';

// ── Generate Tailwind Config ───────────────────────────────────────────────
function toTailwindKey(key) {
  return key.replace(/_/g, '-');
}

function mapColors(c) {
  const out = {};
  for (const [k, v] of Object.entries(c)) {
    if (typeof v === 'string') out[toTailwindKey(k)] = v;
    else if (typeof v === 'object') out[toTailwindKey(k)] = v;
  }
  return out;
}

function mapSpacing(s) {
  const out = {};
  for (const [k, v] of Object.entries(s)) {
    const num = parseFloat(v);
    const unit = typeof v === 'string' ? v.replace(/[\d.-]/g, '') : 'px';
    if (unit === 'px' && !isNaN(num)) {
      out[toTailwindKey(k)] = `${num / 16}rem`;
    } else {
      out[toTailwindKey(k)] = String(v);
    }
  }
  return out;
}

function mapRounded(r) {
  const out = {};
  for (const [k, v] of Object.entries(r)) {
    const num = parseFloat(v);
    const unit = typeof v === 'string' ? v.replace(/[\d.-]/g, '') : 'px';
    if (unit === 'px' && !isNaN(num)) {
      out[toTailwindKey(k)] = `${num / 16}rem`;
    } else {
      out[toTailwindKey(k)] = String(v);
    }
  }
  return out;
}

let tailwindConfig = `/**
 * Tailwind config generated by design-implementer
 * Source: ${path.basename(mdPath)}
 */
module.exports = {
  theme: {
    extend: {
      colors: ${JSON.stringify(mapColors(colors), null, 4)},
      spacing: ${JSON.stringify(mapSpacing(spacing), null, 4)},
      borderRadius: ${JSON.stringify(mapRounded(rounded), null, 4)},
    },
  },
};
`;

// ── Generate CSS Tokens ────────────────────────────────────────────────────
function camelToKebab(str) {
  return str.replace(/([a-z0-9])([A-Z])/g, '$1-$2').replace(/([A-Z]+)([A-Z][a-z])/g, '$1-$2').toLowerCase();
}

function flattenTokens(obj, prefix = '', out = {}) {
  for (const [k, v] of Object.entries(obj)) {
    const key = prefix ? `${prefix}-${camelToKebab(k)}` : camelToKebab(k);
    if (v && typeof v === 'object' && !Array.isArray(v)) {
      flattenTokens(v, key, out);
    } else if (v !== null && v !== undefined) {
      out[key] = String(v);
    }
  }
  return out;
}

let cssTokens = `/* Tokens generated by design-implementer from ${path.basename(mdPath)} */
:root {
`;

const flatColors = flattenTokens(colors, 'color');
for (const [k, v] of Object.entries(flatColors)) cssTokens += `  --${k}: ${v};\n`;

const flatType = flattenTokens(typography, 'text');
for (const [k, v] of Object.entries(flatType)) cssTokens += `  --${k}: ${v};\n`;

const flatSpacing = flattenTokens(spacing, 'space');
for (const [k, v] of Object.entries(flatSpacing)) cssTokens += `  --${k}: ${v};\n`;

const flatRounded = flattenTokens(rounded, 'rounded');
for (const [k, v] of Object.entries(flatRounded)) cssTokens += `  --${k}: ${v};\n`;

cssTokens += '}\n\n';

// ── Component CSS classes ──────────────────────────────────────────────────
if (Object.keys(components).length > 0) {
  cssTokens += '/* Component classes */\n';
  for (const [compName, compDef] of Object.entries(components)) {
    if (typeof compDef !== 'object') continue;
    const className = '.' + camelToKebab(compName).replace(/_/g, '-');
    cssTokens += `${className} {\n`;

    // backgroundColor → background-color
    if (compDef.backgroundColor) {
      const bg = resolveRefs(compDef.backgroundColor, resolved);
      cssTokens += `  background-color: ${bg};\n`;
    }

    // textColor → color
    if (compDef.textColor) {
      const tc = resolveRefs(compDef.textColor, resolved);
      cssTokens += `  color: ${tc};\n`;
    }

    // rounded → border-radius
    if (compDef.rounded) {
      const r = resolveRefs(compDef.rounded, resolved);
      cssTokens += `  border-radius: ${r};\n`;
    }

    // padding
    if (compDef.padding) {
      const p = Array.isArray(compDef.padding) ? compDef.padding.join(' ') : compDef.padding;
      cssTokens += `  padding: ${p};\n`;
    }

    // height
    if (compDef.height) cssTokens += `  height: ${compDef.height};\n`;

    // typography (expanded object)
    if (compDef.typography && typeof compDef.typography === 'object') {
      const t = compDef.typography;
      if (t.fontFamily) cssTokens += `  font-family: ${t.fontFamily};\n`;
      if (t.fontSize) cssTokens += `  font-size: ${t.fontSize};\n`;
      if (t.fontWeight) cssTokens += `  font-weight: ${t.fontWeight};\n`;
      if (t.lineHeight) cssTokens += `  line-height: ${t.lineHeight};\n`;
      if (t.letterSpacing !== undefined) cssTokens += `  letter-spacing: ${t.letterSpacing};\n`;
      if (t.textTransform) cssTokens += `  text-transform: ${t.textTransform};\n`;
    }

    cssTokens += '}\n\n';
  }
}

// ── Write output ───────────────────────────────────────────────────────────
const slug = name.replace(/\s+/g, '-').toLowerCase();

const tailwindFile = path.join(outDir, `${slug}.tailwind.config.js`);
const cssFile = path.join(outDir, `${slug}.tokens.css`);

fs.mkdirSync(outDir, { recursive: true });
fs.writeFileSync(tailwindFile, tailwindConfig, 'utf-8');
fs.writeFileSync(cssFile, cssTokens, 'utf-8');

console.log(`
✓ Generated tokens from ${path.basename(mdPath)}

  Tailwind config  → ${tailwindFile}
  CSS tokens       → ${cssFile}

  Colors:        ${Object.keys(colors).length}
  Typography:    ${Object.keys(typography).length}
  Spacing:       ${Object.keys(spacing).length}
  Border radii:  ${Object.keys(rounded).length}
  Components:    ${Object.keys(components).length}
`);
