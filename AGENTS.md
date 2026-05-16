# AGENTS.md - import-category

## Zweck & Verantwortung

Das `import-category` Modul bietet **Category Import-Funktionalität** für das Pacemaker Import-System. Es ist ein **Tier 4 Modul** und dient als Basis für Category-bezogene Importer.

**Hauptverantwortung:**
- Category Import und Verwaltung
- Category Attributes Import
- URL Rewrite Management
- Repository Pattern für Category-Persistierung
- Service Layer für Category-Verarbeitung
- Observer Pattern für Category-Hooks
- Event-Driven für Bulk Operations
- 3 Dependents (category-ee, converter-product-category)

## Architektur & Design Patterns

### Kern-Klassen
- **CategoryRepository**: Persistierung von Kategorien
- **CategoryAttributeRepository**: Persistierung von Category Attributes
- **CategoryProcessor**: Service Layer für Category-Verarbeitung
- **CategoryObserver**: Observer für Category-Hooks
- **CategoryAttributeObserver**: Observer für Category Attributes

### Verwendete Patterns
- **Observer Pattern**: Für Category-Hooks
- **Repository Pattern**: Für Daten-Persistierung
- **Service Layer**: Für Business Logic
- **Event-Driven**: Für Bulk Operations
- **Factory Pattern**: Für Object-Erstellung

## Abhängigkeiten

### Externe Pakete
- **Keine** - Nur Importer-Implementierungen

### TechDivision Dependencies
- **import** ^18.1 - Core Framework

### Abhängig von diesem Modul (3 Reverse Dependencies)
1. **import-category-ee** - EE Category Extensions
2. **import-converter-product-category** - Product Category Converter
3. **import-cli-simple** - Master CLI

## Wichtige Entry Points

### Repository Klassen
```php
// Category Repository
CategoryRepository::create($row): void
CategoryRepository::update($row): void
CategoryRepository::findByPath($path): array

// Category Attribute Repository
CategoryAttributeRepository::create($row): void
CategoryAttributeRepository::findByCode($code): array
```

### Observer Klassen
```php
// Category Observer
CategoryObserver::handle($row): void

// Category Attribute Observer
CategoryAttributeObserver::handle($row): void
```

## Events & Extension Points

### Events
- **BeforeCategoryCreateEvent**: Vor Category-Create
- **AfterCategoryCreateEvent**: Nach Category-Create
- **BeforeCategoryUpdateEvent**: Vor Category-Update
- **AfterCategoryUpdateEvent**: Nach Category-Update

### Listeners
- **CategoryUrlRewriteListener**: Für URL-Rewrite Management
- **CategoryCacheListener**: Für Category-Caching

## Hints für KI-Agenten

### Wichtig zu verstehen
1. **Tier 4 Modul**: Basis für Category-bezogene Importer
2. **Category-fokussiert**: Spezialisiert auf Category Import
3. **Observer Pattern**: Für Category-Hooks
4. **Repository Pattern**: Für Daten-Persistierung
5. **Event-Driven**: Für Bulk Operations
6. **3 Dependents**: Basis für spezialisierte Importer

### Bei Änderungen
- **Category-Kompatibilität**: Beachte Category-Struktur
- **Observer-Kompatibilität**: Neue Observers sollten optional sein
- **Event-Kompatibilität**: Neue Events sollten optional sein
- **Backward Compatibility**: Alte Imports sollten noch funktionieren

### Implementierungs-Hinweise
- Nutze Observer Pattern für Custom Category-Processing
- Beachte URL-Rewrites bei Category-Imports
- Erwäge Category-Validierung

## Häufige Use Cases

### CSV-Beispiel: Category Import
```csv
entity_id,path,name,level,url_key
1,"1/2","Women",2,"women"
2,"1/2/3","Shirts",3,"women-shirts"
3,"1/2/4","Pants",3,"women-pants"
4,"1/5","Men",2,"men"
```

### Szenarien
1. **Category-Hierarchy Import**: Komplette Kategoriebaum mit URL-Rewrites
2. **Attribute-Update**: Batch-Updates von Category-Attributes
3. **URL-Rewrite-Management**: Automatische URL-Rewrite-Generierung
4. **Multi-Store Categories**: Unterschiedliche Category-Strukturen pro Store

## Performance-Überlegungen

- **Path-Lookup**: Category-Path basierte Suche ist O(n) - langsam bei vielen Kategorien
- **Hierarchy-Build**: Tiefe Bäume (>50 Levels) werden langsam
- **URL-Rewrite-Gen**: Automatische Rewrite-Generierung kostet extra ~2-5ms pro Category
- **Attribute-Insert**: Custom Attributes verdoppeln Insert-Zeit
- **Batch-Optimization**: Optimal 500-1000 Categories pro Batch
- **Memory-Profile**: ~50-100KB pro Category mit Attributes

## Verwandte Module

- **import-category-ee**: EE-spezifische Category-Features
- **import-converter-product-category**: Product-Category Link-Management
- **import**: Core Framework nutzt Category-Repository
- **import-category** ← **diese Datei**

## Troubleshooting & FAQ

**Q: "Category path not found"**
- A: Parent-Kategorie muss vorher importiert sein! Prüfe Import-Reihenfolge: Parent vor Children.

**Q: URL-Rewrites werden nicht erzeugt**
- A: URL-Rewrite-Listener muss registriert sein. Prüfe Event-Listeners in Konfiguration.

**Q: Category-Attribute sind NULL nach Import**
- A: Attributes müssen vorher via `import-attribute` erstellt sein. Prüfe Attribute-Existenz in DB.

**Q: "Duplicate category path"**
- A: Path muss eindeutig sein pro Store! Prüfe: `SELECT * FROM catalog_category_entity WHERE path = ? AND store_id = ?`

## Bekannte Einschränkungen

- **Keine Category-Validierung**: Validierung erfolgt in Importern
- **Keine Product-Category Links**: Links sind in `import-converter-product-category`
- **Keine EE-Features**: EE-Features sind in `import-category-ee`

## Zusammenfassung

`import-category` ist ein **Tier 4 Modul**, das Category Import-Funktionalität für das Pacemaker-System bietet. Es ist die Basis für Category-bezogene Importer und unterstützt Category Attributes und URL-Rewrites.

**Für Agenten:** Verstehe dieses Modul als **Category Importer** mit Observer Pattern, Repository Pattern, und Event-Driven Architektur.
