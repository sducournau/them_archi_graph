# 🛠️ Utilities - Archi Graph Theme

Ce dossier contient des outils de développement, maintenance et débogage pour le thème Archi Graph.

**⚠️ IMPORTANT**: Ces fichiers sont destinés à l'environnement de développement uniquement et ne doivent PAS être déployés en production.

---

## 📁 Structure

```
utilities/
├── maintenance/      # Outils de maintenance WordPress
└── README.md        # Ce fichier
```

---

## 🔧 Maintenance

**Dossier**: `utilities/maintenance/`

Outils pour maintenir et réparer l'installation WordPress :

| Fichier | Description | Quand l'utiliser |
|---------|-------------|------------------|
| `cleanup-broken-media-references.php` | Nettoie les références média cassées | Erreurs 404 dans l'éditeur |
| `clear-cache-full-images.php` | Vide le cache des images complètes | Après modification d'images |
| `clear-wp-cache.php` | Vide tous les caches WordPress | Après changements majeurs |
| `flush-rest-api.php` | Rafraîchit les routes API REST | Après ajout/modification endpoints |
| `flush-rewrite-rules.php` | Rafraîchit les règles de réécriture | Après modification CPT/taxonomies |
| `quick-flush.php` | Flush rapide multi-usage | Dépannage général |
| `fix-htaccess.php` | Répare le fichier .htaccess | Problèmes permaliens |

**⚠️ Attention**: Ces scripts modifient la base de données. Toujours faire un backup avant.

**Comment utiliser:**
```bash
# Méthode 1: Via WP-CLI (recommandé)
wp eval-file utilities/maintenance/flush-rest-api.php

# Méthode 2: Via navigateur (copier temporairement)
cp utilities/maintenance/clear-wp-cache.php ../../../clear-cache-temp.php
# Visiter: http://votresite.local/clear-cache-temp.php
# Supprimer immédiatement après
rm ../../../clear-cache-temp.php
```

### Scripts de Maintenance Courants

#### Problème: Erreurs 404 média dans l'éditeur
```bash
# Visiter via navigateur (admin requis)
# URL: /wp-content/themes/archi-graph-template/utilities/maintenance/cleanup-broken-media-references.php
# Ou utiliser WP-CLI
wp eval-file utilities/maintenance/cleanup-broken-media-references.php
```

#### Problème: Graphique ne s'affiche pas
```bash
wp eval-file utilities/maintenance/flush-rest-api.php
wp eval-file utilities/maintenance/clear-wp-cache.php
```

#### Problème: Erreur 404 sur pages projets
```bash
wp eval-file utilities/maintenance/flush-rewrite-rules.php
```

#### Problème: Images ne se chargent pas
```bash
wp eval-file utilities/maintenance/clear-cache-full-images.php
```

---

##  Fichiers à NE PAS Déployer

**Ces fichiers doivent être exclus du déploiement en production.**

### Via `.gitignore`
```gitignore
/utilities/
```

### Via Script de Déploiement
```bash
# Exemple rsync
rsync -av --exclude='utilities/' theme/ production/
```

### Via FTP
Ne pas uploader le dossier `utilities/` sur le serveur de production.

---

## 📝 Bonnes Pratiques

### 1. Backup Avant Maintenance
```bash
# Backup de la base de données
wp db export backup-$(date +%Y%m%d).sql

# Backup des fichiers
tar -czf theme-backup-$(date +%Y%m%d).tar.gz .
```

### 2. Tests en Local D'abord
- ✅ Toujours tester sur environnement local
- ✅ Vérifier le log `wp-content/debug.log`
- ✅ Tester navigation et fonctionnalités
- ❌ Ne jamais tester directement en production

### 3. Nettoyage Après Usage
```bash
# Supprimer fichiers temporaires
find ../../../ -name "*-temp.php" -delete
```

### 4. Utiliser WP-CLI Quand Possible
```bash
# Préférer
wp eval-file utilities/maintenance/clear-wp-cache.php

# Plutôt que copier dans racine WordPress
```

---

## 🔍 Diagnostic Rapide

### Le graphique ne fonctionne pas

```bash
# 1. Vérifier API REST
curl http://votresite.local/wp-json/archi/v1/articles

# 2. Vider les caches
wp eval-file utilities/maintenance/flush-rest-api.php
wp eval-file utilities/maintenance/clear-wp-cache.php
```

### Les permaliens sont cassés
```bash
wp eval-file utilities/maintenance/flush-rewrite-rules.php
wp rewrite flush
```

### Les métadonnées ne se sauvent pas
```bash
wp eval-file utilities/testing/test-meta-registration.php
wp eval-file utilities/debug/debug-node-sizes.php
```

---

## 🆘 Support

Si un outil ne fonctionne pas :

1. **Vérifier les logs WordPress**: `wp-content/debug.log`
2. **Vérifier la console navigateur**: F12 → Console
3. **Consulter la documentation principale**: `../docs/`
4. **Vérifier les permissions fichiers**: `chmod 644 utilities/**/*.php`

---

## 📚 Documentation Liée

- **Documentation principale**: `../docs/README.md`
- **Guide de configuration**: `../docs/01-getting-started/installation.md`
- **Dépannage**: `../docs/05-development/troubleshooting.md`
- **Guide développeur**: `../docs/05-development/developer-guide.md`

---

## ⚖️ Licence

Ces utilitaires font partie du thème Archi Graph et sont distribués sous licence GPL v3.

---

**Dernière mise à jour**: 4 novembre 2025  
**Version du thème**: 1.1.0
