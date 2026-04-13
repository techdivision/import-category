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

## Bekannte Einschränkungen

- **Keine Category-Validierung**: Validierung erfolgt in Importern
- **Keine Product-Category Links**: Links sind in `import-converter-product-category`
- **Keine EE-Features**: EE-Features sind in `import-category-ee`

## Zusammenfassung

`import-category` ist ein **Tier 4 Modul**, das Category Import-Funktionalität für das Pacemaker-System bietet. Es ist die Basis für Category-bezogene Importer und unterstützt Category Attributes und URL-Rewrites.

**Für Agenten:** Verstehe dieses Modul als **Category Importer** mit Observer Pattern, Repository Pattern, und Event-Driven Architektur.
