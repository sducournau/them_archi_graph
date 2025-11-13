#!/bin/bash

# Script pour harmoniser toutes les valeurs par défaut de node_size à 80px
# Date: 2025-11-13

echo "🔍 Recherche des valeurs incohérentes de node_size..."

# Fichiers à corriger
FILES=(
  "assets/js/utils/physicsUtils.js"
  "assets/js/utils/nodeVisualEffects.js"
  "assets/js/utils/nodeInteractions.js"
  "assets/js/utils/GraphManager.js"
  "assets/js/utils/graphHelpers.js"
  "assets/js/utils/dataFetcher.js"
  "assets/js/utils/categoryColors.js"
  "assets/js/utils/advancedShapes.js"
  "assets/js/components/Node.jsx"
)

echo ""
echo "📝 Fichiers à corriger:"
for file in "${FILES[@]}"; do
  if [ -f "$file" ]; then
    count=$(grep -c "node_size || [0-9]\\+" "$file" 2>/dev/null || echo "0")
    if [ "$count" -gt 0 ]; then
      echo "  ✓ $file ($count occurrences)"
    fi
  fi
done

echo ""
echo "⚠️  ATTENTION: Ce script va remplacer:"
echo "  - || 60 → || 80"
echo "  - || 120 → || 80"
echo ""
read -p "Continuer? (y/N) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
  echo "❌ Annulé"
  exit 1
fi

echo ""
echo "🔧 Application des corrections..."

# Remplacer || 60 par || 80
for file in "${FILES[@]}"; do
  if [ -f "$file" ]; then
    sed -i 's/node_size || 60/node_size || 80/g' "$file"
    sed -i 's/defaultNodeSize || 60/defaultNodeSize || 80/g' "$file"
    echo "  ✓ $file"
  fi
done

# Remplacer || 120 par || 80 dans graphHelpers.js
if [ -f "assets/js/utils/graphHelpers.js" ]; then
  sed -i 's/node_size || 120/node_size || 80/g' "assets/js/utils/graphHelpers.js"
  echo "  ✓ graphHelpers.js (120 → 80)"
fi

echo ""
echo "✅ Corrections appliquées!"
echo ""
echo "🔍 Vérification..."
grep -n "node_size || " assets/js/**/*.{js,jsx} 2>/dev/null | grep -v "|| 80" || echo "  ✓ Toutes les valeurs sont à 80"

echo ""
echo "📦 N'oubliez pas de rebuild les assets:"
echo "  npm run build"
