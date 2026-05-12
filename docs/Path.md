# `Phyx\Path`

> Couche path de Phyx : une **façade statique** `Phyx\Path` pour manipuler des chemins de fichiers sans confondre
> chaîne brute, chemin logique et accès au disque.
>
> 8 traits composés dans `Phyx\Path`, plusieurs enums dans `Phyx\Enums\`, ~50 méthodes publiques couvrant jointure,
> normalisation, segments, extensions, noms, parents, styles Windows/Unix, résolution et matching.

## Principe directeur

Un chemin n'est pas juste une string. Phyx sépare les opérations purement syntaxiques (`join`, `normalize`, `extension`)
des opérations qui touchent réellement le système de fichiers (`real`, `exists`, `isFile`) afin d'éviter les
side-effects cachés.

Par défaut, `Path` ne lit pas le disque. Toute méthode qui interroge le filesystem doit être clairement nommée et
documentée.

## Conventions transversales

### Signature

- `public static`, `declare(strict_types=1)` partout.
- Les chemins sont des `string`, mais les retours impossibles sont `null`, jamais `false`.
- Les méthodes syntaxiques n'appellent pas `realpath`.
- Les méthodes de résolution physique retournent `?string`.
- Les séparateurs sont normalisés via enum ou détection explicite.

### Retours prédictibles

| Cas                         | Retour             |
|-----------------------------|--------------------|
| Chemin transformé           | `string`           |
| Segment absent              | `null`             |
| Liste de segments           | `list<string>`     |
| Prédicat                    | `bool`             |
| Résolution impossible       | `null`             |

### Vocabulaire fixe des paramètres

| Param        | Sens                          | Position        |
|--------------|-------------------------------|-----------------|
| `$path`      | Chemin cible                  | Toujours 1er    |
| `$paths`     | Liste de chemins              | 1er pour join   |
| `$segment`   | Segment de chemin             | selon méthode   |
| `$extension` | Extension sans point          | après `$path`   |
| `$style`     | Style de séparateur           | dernier         |
| `$base`      | Chemin de base                | 2e              |

### Nommage des méthodes

| PHP natif              | Phyx               |
|------------------------|--------------------|
| `basename`             | `basename`         |
| `dirname`              | `dirname`          |
| `pathinfo`             | `info`             |
| `realpath`             | `real`             |
| `glob`                 | `glob`             |
| `fnmatch`              | `matches`          |

## Enums (sous `Phyx\Enums\`)

| Enum         | Valeurs                         | Utilisé par                  |
|--------------|---------------------------------|------------------------------|
| `PathStyle`  | `Unix`, `Windows`, `Native`     | `normalize`, `join`          |
| `PathPart`   | `Dirname`, `Basename`, `Filename`, `Extension` | `info`        |
| `PathRoot`   | `None`, `Unix`, `WindowsDrive`, `Unc` | `root`, `isAbsolute` |
| `MatchMode`  | `Glob`, `Regex`, `Literal`      | `matches`                    |

## Traits (sous `Phyx\Path\`)

| # | Trait              | Responsabilité                                |
|---|--------------------|-----------------------------------------------|
| 1 | `HandleJoin`       | Jointure et concaténation de chemins          |
| 2 | `HandleNormalize`  | Nettoyage des séparateurs et segments         |
| 3 | `HandleInspect`    | Absolu, relatif, racine, style                |
| 4 | `HandleParts`      | Nom, extension, dirname, basename             |
| 5 | `HandleSegments`   | Segments, parent, ancestors                   |
| 6 | `HandleTransform`  | Changement d'extension, relatif, unix/windows |
| 7 | `HandleResolve`    | Résolution filesystem explicite               |
| 8 | `HandleMatch`      | Glob, pattern, matching                       |

## API reference

### `HandleJoin` - jointure

| Phyx                              | PHP natif / logique | Retour |
|-----------------------------------|---------------------|--------|
| `Path::join(...$paths)`           | dérivé              | `string` |
| `Path::append($path, ...$segments)` | dérivé            | `string` |
| `Path::prepend($path, $base)`     | dérivé              | `string` |

### `HandleNormalize` - normalisation

| Phyx                                | PHP natif / logique | Retour |
|-------------------------------------|---------------------|--------|
| `Path::normalize($path, $style)`    | dérivé              | `string` |
| `Path::normalizeSeparators($path, $style)` | dérivé       | `string` |
| `Path::removeTrailingSeparator($path)` | dérivé           | `string` |
| `Path::ensureTrailingSeparator($path)` | dérivé           | `string` |

### `HandleInspect` - inspection

| Phyx                         | PHP natif / logique | Retour |
|------------------------------|---------------------|--------|
| `Path::isAbsolute($path)`    | dérivé              | `bool` |
| `Path::isRelative($path)`    | dérivé              | `bool` |
| `Path::isRoot($path)`        | dérivé              | `bool` |
| `Path::root($path)`          | dérivé              | `?string` |
| `Path::style($path)`         | dérivé              | `PathStyle` |

### `HandleParts` - parties du chemin

| Phyx                              | PHP natif  | Retour |
|-----------------------------------|------------|--------|
| `Path::info($path)`               | `pathinfo` | `array<string, string>` |
| `Path::dirname($path, $levels)`   | `dirname`  | `string` |
| `Path::basename($path, $suffix)`  | `basename` | `string` |
| `Path::filename($path)`           | `pathinfo` | `string` |
| `Path::extension($path)`          | `pathinfo` | `?string` |

### `HandleSegments` - segments

| Phyx                              | PHP natif / logique | Retour |
|-----------------------------------|---------------------|--------|
| `Path::segments($path)`           | dérivé              | `list<string>` |
| `Path::parent($path)`             | `dirname`           | `?string` |
| `Path::ancestors($path)`          | dérivé              | `list<string>` |
| `Path::lastSegment($path)`        | `basename`          | `?string` |

### `HandleTransform` - transformations

| Phyx                                   | PHP natif / logique | Retour |
|----------------------------------------|---------------------|--------|
| `Path::withExtension($path, $extension)` | dérivé            | `string` |
| `Path::withoutExtension($path)`        | dérivé              | `string` |
| `Path::toUnix($path)`                  | dérivé              | `string` |
| `Path::toWindows($path)`               | dérivé              | `string` |
| `Path::relative($path, $base)`         | dérivé              | `string` |

### `HandleResolve` - filesystem explicite

| Phyx                         | PHP natif     | Retour |
|------------------------------|---------------|--------|
| `Path::real($path)`          | `realpath`    | `?string` |
| `Path::exists($path)`        | `file_exists` | `bool` |
| `Path::isFile($path)`        | `is_file`     | `bool` |
| `Path::isDirectory($path)`   | `is_dir`      | `bool` |

### `HandleMatch` - glob et matching

| Phyx                         | PHP natif | Retour |
|------------------------------|-----------|--------|
| `Path::glob($pattern)`       | `glob`    | `list<string>` |
| `Path::matches($path, $pattern, $mode)` | `fnmatch` / regex | `bool` |

## Exclusions

- Lecture et écriture de fichiers : domaine `File`.
- URLs : domaine `Url`.
- Sécurité d'accès disque, permissions avancées, streams : domaine `FileSystem` ou `File`.

## Tests

- 1 fichier de test par trait sous `tests/Path/`.
- Cas obligatoires : chemin vide, `/`, `C:\`, UNC, chemins mixtes Windows/Unix, extensions multiples, chemin sans
  extension, trailing slash.
- Les méthodes filesystem utilisent des fixtures temporaires.
