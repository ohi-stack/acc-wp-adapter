#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DIST_DIR="$ROOT_DIR/dist"
PLUGIN_SRC="$ROOT_DIR/plugin"
PLUGIN_SLUG="algonquian-real-estate-platform"
BUILD_DIR="$DIST_DIR/$PLUGIN_SLUG"
ZIP_FILE="$DIST_DIR/$PLUGIN_SLUG.zip"

rm -rf "$BUILD_DIR" "$ZIP_FILE"
mkdir -p "$BUILD_DIR" "$DIST_DIR"

rsync -a \
  --exclude='.git' \
  --exclude='.github' \
  --exclude='tests' \
  --exclude='dist' \
  --exclude='release-builder' \
  "$PLUGIN_SRC/" "$BUILD_DIR/"

if [ ! -f "$BUILD_DIR/algonquian-real-estate.php" ]; then
  echo "Missing main plugin bootstrap: algonquian-real-estate.php" >&2
  exit 1
fi

cd "$DIST_DIR"
zip -r "$ZIP_FILE" "$PLUGIN_SLUG" >/dev/null

echo "Built $ZIP_FILE"
