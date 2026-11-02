#!/bin/bash
set -e
# Fix Windows symlink block for vercel-php builder
if [ -n "$VERCEL_BUILDER_LOCALS" ]; then
  # Linux build path (builder container) — no fix needed
  :
fi
# Patch the installed vercel-php runtime handler (workaround for github vercel-community/php#650)
PHP_FILE=$(find /vercel -name "index.js" -path "*vercel-php*" 2>/dev/null | head -n1 || true)
if [ -n "$PHP_FILE" ]; then
  sed -i "s/handler: 'launcher.launcher'/handler: 'launcher.js'/g" "$PHP_FILE"
  echo "Patched vercel-php handler: $PHP_FILE"
fi
