/**
 * Convert source images (PNG / JPEG) into compressed WebP files written to
 * public/storage/uploads/. Driven by a JSON manifest written by
 * PublicAssetImporter so PHP stays the single source of truth for the
 * source -> target mapping.
 *
 * The manifest argument is a path to a JSON file with the shape:
 *
 *   [
 *     { "src": "absolute/source/path", "dst": "absolute/target.webp" },
 *     ...
 *   ]
 *
 * sharp is resolved via the standard Node module resolution, with optional
 * fallback to the sibling serenbluesafaris frontend's node_modules. Override
 * with SBS_NODE_MODULES_PATH when the frontend lives elsewhere.
 */

import { createRequire } from 'node:module';
import { existsSync, statSync, mkdirSync, readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';

const manifestPath = process.argv[2];
if (!manifestPath) {
  console.error('Usage: node convert-public-assets.mjs <manifest.json>');
  process.exit(2);
}

const entries = JSON.parse(readFileSync(manifestPath, 'utf8'));
const quality = Number(process.env.SBS_WEBP_QUALITY ?? 78);
const maxWidth = Number(process.env.SBS_WEBP_MAX_WIDTH ?? 1920);

const sharp = await loadSharp();

let converted = 0;
let skipped = 0;
let missing = 0;

for (const { src, dst } of entries) {
  if (!existsSync(src)) {
    console.warn(`  miss   ${src}`);
    missing++;
    continue;
  }

  if (existsSync(dst) && statSync(dst).mtimeMs >= statSync(src).mtimeMs) {
    skipped++;
    continue;
  }

  mkdirSync(dirname(dst), { recursive: true });

  try {
    await sharp(src)
      .rotate()
      .resize({ width: maxWidth, withoutEnlargement: true })
      .webp({ quality, effort: 5 })
      .toFile(dst);
    const before = statSync(src).size;
    const after = statSync(dst).size;
    const saved = Math.max(0, before - after);
    console.log(
      `  ok     ${dst}  (${formatBytes(before)} -> ${formatBytes(after)}, -${formatBytes(saved)})`
    );
    converted++;
  } catch (err) {
    console.error(`  fail   ${src}: ${err.message}`);
    process.exitCode = 1;
  }
}

console.log(`\nconverted=${converted}  skipped=${skipped}  missing=${missing}`);

async function loadSharp() {
  const require = createRequire(import.meta.url);
  const candidates = [];

  if (process.env.SBS_NODE_MODULES_PATH) {
    candidates.push(resolve(process.env.SBS_NODE_MODULES_PATH, 'sharp'));
  }

  candidates.push('sharp');
  candidates.push(resolve(import.meta.dirname ?? __dirname, '..', '..', '..', 'node_modules', 'sharp'));
  candidates.push(resolve(import.meta.dirname ?? __dirname, '..', '..', '..', '..', 'serenbluesafaris', 'node_modules', 'sharp'));

  let lastError;
  for (const candidate of candidates) {
    try {
      const mod = require(candidate);
      return mod.default ?? mod;
    } catch (err) {
      lastError = err;
    }
  }

  console.error(
    'sharp not found. Tried:\n  - ' +
      candidates.join('\n  - ') +
      '\nInstall it locally (npm i sharp) or set SBS_NODE_MODULES_PATH to a node_modules folder that contains sharp.'
  );
  console.error(lastError?.message);
  process.exit(2);
}

function formatBytes(n) {
  if (n < 1024) return `${n}B`;
  if (n < 1024 * 1024) return `${(n / 1024).toFixed(1)}KB`;
  return `${(n / 1024 / 1024).toFixed(2)}MB`;
}
