# `Phyx\Arr`

> Couche array de Phyx : une **façade statique** `Phyx\Arr` qui réécrit les fonctions natives PHP autour d'une API
> cohérente, prédictible, typée PHPDoc, sans mutation implicite.
>
> 12 traits composés dans `Phyx\Arr`, plusieurs enums orthogonaux dans `Phyx\Enums\`, ~90 méthodes publiques couvrant
> les usages courants des tableaux PHP : accès, inspection, transformation, filtrage, tri, regroupement, set operations,
> chemins imbriqués et conversions.

## Principe directeur

PHP utilise `array` pour deux structures différentes : la liste ordonnée et la map associative. Phyx doit rendre cette
différence explicite dès que possible.

On ne reproduit pas les noms natifs (`array_key_exists`, `array_column`, `array_filter`, `array_reduce`). On choisit un
vocabulaire plus court et plus constant : `has`, `pluck`, `filter`, `reduce`, `groupBy`, `keyBy`, `flatten`.

## Conventions transversales

### Signature

- `public static`, `declare(strict_types=1)` partout.
- Pas de mutation implicite : toute méthode retourne un nouveau tableau ou une valeur.
- `mixed` est accepté uniquement pour représenter les valeurs contenues dans un tableau PHP.
- Les collections retournées sont documentées en PHPDoc : `list<T>`, `array<TKey, TValue>`, `non-empty-list<T>` quand
  possible.
- Les callbacks reçoivent toujours la valeur en premier, puis la clé : `fn ($value, $key) => ...`.
- Les méthodes de chemin utilisent le paramètre `$path` et le séparateur `.` par défaut, jamais une syntaxe magique
  implicite non documentée.

### Retours prédictibles

| Cas                           | Retour                                  |
|-------------------------------|-----------------------------------------|
| Valeur absente                | `$default` ou `null` selon la méthode   |
| Recherche non trouvée         | `null`                                  |
| Transformation de collection  | `array`                                 |
| Extraction de liste           | `list<mixed>`                           |
| Prédicat                      | `bool`                                  |
| Réduction                     | valeur de l'accumulateur                |

### Vocabulaire fixe des paramètres

| Param          | Sens                                      | Position                |
|----------------|-------------------------------------------|-------------------------|
| `$array`       | Tableau cible                             | Toujours 1er            |
| `$key`         | Clé directe                               | 2e                      |
| `$path`        | Chemin imbriqué                           | 2e                      |
| `$value`       | Valeur à chercher, insérer ou comparer    | selon méthode           |
| `$default`     | Valeur de secours                         | dernier ou avant enum   |
| `$callback`    | Fonction de transformation ou prédicat    | après `$array`          |
| `$separator`   | Séparateur de chemin                      | dernier                 |
| `$preserveKeys`| Conservation des clés                     | dernier bool nommé      |
| `$direction`   | `SortDirection`                           | avant `$preserveKeys`   |
| `$mode`        | Enum de stratégie                         | dernier                 |

### Nommage des méthodes

- camelCase, anglais, court, sans préfixe `array`.
- Les méthodes qui travaillent sur les clés contiennent `Key`.
- Les méthodes qui travaillent sur des chemins imbriqués contiennent `Path` ou utilisent explicitement `$path`.
- Les méthodes qui changent la forme du tableau utilisent un verbe clair : `flatten`, `collapse`, `chunk`, `wrap`.

| PHP natif                         | Phyx                 |
|-----------------------------------|----------------------|
| `array_key_exists`                | `hasKey`             |
| `in_array`                        | `contains`           |
| `array_search`                    | `indexOf`            |
| `array_column`                    | `pluck`              |
| `array_map`                       | `map`                |
| `array_filter`                    | `filter`             |
| `array_reduce`                    | `reduce`             |
| `array_values`                    | `values`             |
| `array_keys`                      | `keys`               |
| `array_unique`                    | `unique`             |
| `array_merge`                     | `merge`              |
| `array_replace_recursive`         | `replaceRecursive`   |
| `array_diff`                      | `diff`               |
| `array_intersect`                 | `intersect`          |
| `array_multisort` / `uasort`      | `sortBy`             |

### PHPDoc obligatoire

Chaque méthode publique porte un bloc PHPDoc complet :

- 1ere ligne impérative courte (< 100 chars).
- Paragraphe décrivant conservation des clés, comparaison stricte, mutation ou absence de mutation.
- Un `@template` dès qu'une méthode conserve ou transforme un type.
- Un `@param` par argument, description complète.
- `@return` toujours présent.
- `@throws` quand applicable.
- `@example` annoté (`// =>` pour les résultats).
- `@see` vers enums liés + fonction PHP native équivalente.

## Enums (sous `Phyx\Enums\`)

| Enum             | Valeurs                                      | Utilisé par                         |
|------------------|----------------------------------------------|-------------------------------------|
| `SortDirection`  | `Ascending`, `Descending`                    | `sort`, `sortBy`, `sortKeys`        |
| `SortMode`       | `Regular`, `Numeric`, `String`, `Natural`    | `sort`, `sortKeys`, `unique`        |
| `Comparison`     | `Loose`, `Strict`                            | `contains`, `indexOf`, `unique`     |
| `MissingValue`   | `Null`, `Throw`, `Default`                   | `get`, `first`, `last`              |
| `MergeStrategy`  | `Overwrite`, `Append`, `Recursive`           | `merge`, `mergeRecursive`           |

## Traits (sous `Phyx\Arr\`)

Façade `Phyx\Arr` = `use` de 12 traits, un trait = une responsabilité unique.

| #  | Trait             | Responsabilité                                               |
|----|-------------------|--------------------------------------------------------------|
| 1  | `HandleAccess`    | Accès direct et chemins imbriqués                            |
| 2  | `HandleInspect`   | Nature du tableau, taille, clés, valeurs                     |
| 3  | `HandleSearch`    | Présence, recherche, premier/dernier élément                 |
| 4  | `HandleTransform` | Map, filter, reject, reduce                                  |
| 5  | `HandleShape`     | Forme : flatten, collapse, chunk, wrap                       |
| 6  | `HandleGroup`     | Regroupement, indexation, pluck, partition                   |
| 7  | `HandleSort`      | Tri par valeur, clé, callback, naturel                       |
| 8  | `HandleSet`       | Unique, diff, intersect, union                               |
| 9  | `HandleMerge`     | Merge, replace, append, prepend                              |
| 10 | `HandleRandom`    | Random, shuffle, sample                                      |
| 11 | `HandleWalk`      | Traversée récursive, dot/undot                               |
| 12 | `HandleCombine`   | Zip, combine, pair, transpose                                |

Aucune dépendance croisée entre traits ; le vocabulaire partagé est `Phyx\Enums\*`.

## API reference

### `HandleAccess` - accès direct et chemins imbriqués

| Phyx                                                | PHP natif / logique        | Retour                    |
|-----------------------------------------------------|----------------------------|---------------------------|
| `Arr::get($array, $key, $default)`                  | `$array[$key] ?? ...`      | `mixed`                   |
| `Arr::getPath($array, $path, $default, $separator)` | dérivé                     | `mixed`                   |
| `Arr::set($array, $key, $value)`                    | dérivé                     | `array`                   |
| `Arr::setPath($array, $path, $value, $separator)`   | dérivé                     | `array`                   |
| `Arr::forget($array, $key)`                         | `unset` safe               | `array`                   |
| `Arr::forgetPath($array, $path, $separator)`        | dérivé                     | `array`                   |
| `Arr::hasKey($array, $key)`                         | `array_key_exists`         | `bool`                    |
| `Arr::hasPath($array, $path, $separator)`           | dérivé                     | `bool`                    |

### `HandleInspect` - nature, clés, valeurs

| Phyx                                  | PHP natif            | Retour            |
|---------------------------------------|----------------------|-------------------|
| `Arr::isList($array)`                 | `array_is_list`      | `bool`            |
| `Arr::isAssoc($array)`                | dérivé               | `bool`            |
| `Arr::isEmpty($array)`                | `count`              | `bool`            |
| `Arr::isNotEmpty($array)`             | `count`              | `bool`            |
| `Arr::count($array)`                  | `count`              | `int`             |
| `Arr::keys($array)`                   | `array_keys`         | `list<array-key>` |
| `Arr::values($array)`                 | `array_values`       | `list<mixed>`     |
| `Arr::firstKey($array)`               | `array_key_first`    | `array-key|null`  |
| `Arr::lastKey($array)`                | `array_key_last`     | `array-key|null`  |

### `HandleSearch` - présence et recherche

| Phyx                                             | PHP natif              | Retour           |
|--------------------------------------------------|------------------------|------------------|
| `Arr::contains($array, $value, $comparison)`     | `in_array`             | `bool`           |
| `Arr::indexOf($array, $value, $comparison)`      | `array_search`         | `array-key|null` |
| `Arr::first($array, $callback, $default)`        | dérivé                 | `mixed`          |
| `Arr::last($array, $callback, $default)`         | dérivé                 | `mixed`          |
| `Arr::where($array, $callback, $preserveKeys)`   | `array_filter`         | `array`          |
| `Arr::containsKey($array, $key)`                 | `array_key_exists`     | `bool`           |

### `HandleTransform` - transformation

| Phyx                                      | PHP natif       | Retour |
|-------------------------------------------|-----------------|--------|
| `Arr::map($array, $callback)`             | `array_map`     | `array`|
| `Arr::mapKeys($array, $callback)`         | dérivé          | `array`|
| `Arr::mapWithKeys($array, $callback)`     | dérivé          | `array`|
| `Arr::filter($array, $callback)`          | `array_filter`  | `array`|
| `Arr::reject($array, $callback)`          | dérivé          | `array`|
| `Arr::reduce($array, $callback, $initial)`| `array_reduce`  | `mixed`|

### `HandleShape` - forme du tableau

| Phyx                                      | PHP natif / logique | Retour |
|-------------------------------------------|---------------------|--------|
| `Arr::flatten($array, $depth)`            | dérivé              | `array`|
| `Arr::collapse($array)`                   | dérivé              | `array`|
| `Arr::chunk($array, $size, $preserveKeys)`| `array_chunk`       | `array`|
| `Arr::slice($array, $offset, $length)`    | `array_slice`       | `array`|
| `Arr::take($array, $length)`              | `array_slice`       | `array`|
| `Arr::wrap($value)`                       | dérivé              | `array`|

### `HandleGroup` - regroupement et extraction

| Phyx                                      | PHP natif / logique | Retour |
|-------------------------------------------|---------------------|--------|
| `Arr::pluck($array, $valueKey, $keyKey)`  | `array_column`      | `array`|
| `Arr::groupBy($array, $callback)`         | dérivé              | `array`|
| `Arr::keyBy($array, $callback)`           | dérivé              | `array`|
| `Arr::partition($array, $callback)`       | dérivé              | `array{0: array, 1: array}` |

### `HandleSort` - tri

| Phyx                                                   | PHP natif         | Retour |
|--------------------------------------------------------|-------------------|--------|
| `Arr::sort($array, $direction, $mode, $preserveKeys)`  | `sort` / `asort`  | `array`|
| `Arr::sortKeys($array, $direction, $mode)`             | `ksort` / `krsort`| `array`|
| `Arr::sortBy($array, $callback, $direction)`           | `uasort`          | `array`|
| `Arr::reverse($array, $preserveKeys)`                  | `array_reverse`   | `array`|

### `HandleSet` - opérations d'ensemble

| Phyx                                      | PHP natif           | Retour |
|-------------------------------------------|---------------------|--------|
| `Arr::unique($array, $comparison)`        | `array_unique`      | `array`|
| `Arr::duplicates($array, $comparison)`    | dérivé              | `array`|
| `Arr::diff($array, ...$others)`           | `array_diff`        | `array`|
| `Arr::intersect($array, ...$others)`      | `array_intersect`   | `array`|
| `Arr::union($array, ...$others)`          | opérateur `+`       | `array`|

### `HandleMerge` - assemblage

| Phyx                                      | PHP natif                  | Retour |
|-------------------------------------------|----------------------------|--------|
| `Arr::merge($array, ...$others)`          | `array_merge`              | `array`|
| `Arr::mergeRecursive($array, ...$others)` | `array_merge_recursive`    | `array`|
| `Arr::replace($array, ...$others)`        | `array_replace`            | `array`|
| `Arr::replaceRecursive($array, ...$others)`| `array_replace_recursive` | `array`|
| `Arr::append($array, $value)`             | dérivé                     | `array`|
| `Arr::prepend($array, $value, $key)`      | dérivé                     | `array`|

### `HandleRandom` - hasard

| Phyx                              | PHP natif          | Retour |
|-----------------------------------|--------------------|--------|
| `Arr::random($array)`             | `array_rand`       | `mixed`|
| `Arr::sample($array, $count)`     | `array_rand`       | `array`|
| `Arr::shuffle($array)`            | `shuffle`          | `array`|

### `HandleWalk` - récursif et chemins

| Phyx                                      | PHP natif / logique | Retour |
|-------------------------------------------|---------------------|--------|
| `Arr::dot($array, $separator)`            | dérivé              | `array<string, mixed>` |
| `Arr::undot($array, $separator)`          | dérivé              | `array`|
| `Arr::walk($array, $callback)`            | `array_walk`        | `array`|
| `Arr::walkRecursive($array, $callback)`   | `array_walk_recursive` | `array`|

### `HandleCombine` - combinaison

| Phyx                                      | PHP natif / logique | Retour |
|-------------------------------------------|---------------------|--------|
| `Arr::combine($keys, $values)`            | `array_combine`     | `array`|
| `Arr::zip(...$arrays)`                    | dérivé              | `list<array>` |
| `Arr::pair($array)`                       | dérivé              | `array{0: mixed, 1: mixed}` |
| `Arr::transpose($rows)`                   | dérivé              | `array`|

## Polyfills

- `array_is_list` si le minimum supporté descend sous PHP 8.1.
- Helpers internes de comparaison stricte pour éviter les surprises de `array_unique`.

## Tests

- 1 fichier de test par trait sous `tests/Arr/`.
- Cible 100 % lines + 100 % branches sur `src/`.
- Pour chaque méthode publique : tableau vide, liste simple, tableau associatif, clés numériques, clés string,
  valeurs `null`, comparaison stricte et non stricte quand applicable.
- Assertions strictes (`assertSame`) et `#[DataProvider]` pour les variations d'enum.
