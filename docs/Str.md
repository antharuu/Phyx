# `Phyx\Str`

> Couche string de Phyx : une **façade statique** `Phyx\Str` qui réécrit les fonctions natives PHP autour d'une API
> propre, prédictible, multibyte par défaut.
>
> 14 traits composés dans `Phyx\Str`, 4 enums orthogonaux dans `Phyx\Enums\`, ~75 méthodes publiques couvrant ~85
> fonctions natives. La PHPDoc inline est la source de vérité — ce document donne le cadrage, les conventions et la
> liste
> rapide des méthodes.

## Principe directeur

On ne reproduit pas l'API PHP native. On la **réécrit** pour qu'elle soit cohérente, devinable et lisible. Si le nom
natif est mauvais (`strrev`, `strpos`, `ucwords`, `nl2br`), on choisit un nom Phyx clair. Si l'ordre des paramètres
natifs est incohérent (`strpos($haystack, $needle)` vs `array_search($needle, $haystack)`), on fixe un seul ordre Phyx
et on s'y tient sur **toutes** les méthodes.

## Conventions transversales

### Signature

- `public static`, `declare(strict_types=1)` partout, types stricts (`?int` plutôt que `false|int`, jamais `mixed`).
- Multibyte par défaut : `mb_strlen`, `mb_strpos`, `mb_strtolower`, `mb_str_split` ; l'encodage est sélectionné via le
  paramètre **`Encoding $encoding = Encoding::Utf8`** (jamais de string `'UTF-8'` hardcodée à l'usage).
- Pas de side-effect (les fonctions qui écrivent dans un flux — `printf`, `fprintf`, `vprintf` — sont **exclues**, on
  garde uniquement les variantes qui retournent une string).

### Retours prédictibles

| Cas                          | Retour                       |
|------------------------------|------------------------------|
| Position « non trouvé »      | `null` (jamais `false`/`-1`) |
| Position / comptage / taille | `int`                        |
| Transformation               | `string`                     |
| Split                        | `list<string>`               |
| Prédicat                     | `bool`                       |

### Vocabulaire fixe des paramètres

| Param            | Sens                                    | Position                                         |
|------------------|-----------------------------------------|--------------------------------------------------|
| `$value`         | La chaîne cible                         | **Toujours 1ʳᵉ**                                 |
| `$search`        | Ce qu'on cherche / extrait              | 2ᵉ                                               |
| `$replacement`   | Ce avec quoi on remplace                | 3ᵉ                                               |
| `$length`        | Taille cible (entier)                   | selon méthode                                    |
| `$with`          | Avec quoi remplir / rembourrer          | après `$length`                                  |
| `$separator`     | Délimiteur pour split/join              | 2ᵉ                                               |
| `$chars`         | Set de caractères (trim, span…)         | 2ᵉ                                               |
| `$start`, `$end` | Bornes d'extraction                     | 2ᵉ et 3ᵉ                                         |
| `$case`          | `CaseSensitivity`                       | **avant-dernier**                                |
| `$side`          | `Side`                                  | **dernier** (méthodes sans `$encoding`)          |
| `$ordering`      | `Ordering`                              | **avant-dernier**                                |
| `$encoding`      | `Encoding` (toujours nommé `$encoding`) | **dernier** sur toute méthode qui appelle `mb_*` |

### Nommage des méthodes

- camelCase, anglais, verbe ou prédicat.
- Aucune référence au sigle `str_`, `mb_`, ni suffixe `*I` / `*Ci` / `*Nat` (la variation passe par enum).
- Renommages standard depuis PHP natif :

| PHP natif              | Phyx                                 |
|------------------------|--------------------------------------|
| `strrev`               | `reverse`                            |
| `strpos` / `stripos`   | `indexOf` (+ enum `CaseSensitivity`) |
| `strrpos` / `strripos` | `lastIndexOf`                        |
| `strstr` / `stristr`   | `firstOf`                            |
| `ucwords`              | `capitalizeWords`                    |
| `lcfirst`              | `decapitalize`                       |
| `nl2br`                | `linebreaksToBr`                     |
| `htmlspecialchars`     | `escapeHtml`                         |
| `addslashes`           | `addSlashes`                         |
| `bin2hex`              | `toHex`                              |
| `chunk_split`          | `chunk`                              |
| `wordwrap`             | `wrap`                               |

### PHPDoc obligatoire

Chaque méthode publique porte un bloc PHPDoc complet :

- 1ʳᵉ ligne impérative courte (< 100 chars).
- Paragraphe décrivant comportement subtil, multibyte, divergence avec le natif PHP.
- Un `@param` par argument, description complète.
- `@return` toujours présent.
- `@throws` quand applicable.
- `@example` annoté (`// =>` pour les résultats).
- `@see` vers enums liés + fonction PHP native équivalente.

## Enums (sous `Phyx\Enums\`)

| Enum              | Valeurs                                                                                                                                 | Utilisé par                                |
|-------------------|-----------------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------|
| `CaseSensitivity` | `Sensitive`, `Insensitive`                                                                                                              | `contains`, `indexOf`, `compare`, …        |
| `Side`            | `Start`, `End`, `Both`                                                                                                                  | `trim`, `pad`                              |
| `Ordering`        | `Binary`, `Natural`, `Locale`                                                                                                           | `compare`                                  |
| `Encoding`        | ~20 cases (Utf8, Utf16Be/Le, Utf32Be/Le, Ascii, Iso8859-1/-2/-5/-6/-7/-8/-15, Windows-1251/-1252/-1254, ShiftJis, EucJp, Big5, Gb18030) | toute méthode appelant une fonction `mb_*` |

## Traits (sous `Phyx\Str\`)

Façade `Phyx\Str` = `use` de 14 traits, un trait = une responsabilité unique.

| #  | Trait           | Responsabilité                                                                         |
|----|-----------------|----------------------------------------------------------------------------------------|
| 1  | `HandleSearch`  | Présence / position / sous-chaîne trouvée                                              |
| 2  | `HandleCase`    | Casse des lettres                                                                      |
| 3  | `HandleTrim`    | Retrait de caractères en bord (utilise `Side`)                                         |
| 4  | `HandleLength`  | Mesure & inventaire                                                                    |
| 5  | `HandleSlice`   | Extraction de portions                                                                 |
| 6  | `HandleReplace` | Remplacement                                                                           |
| 7  | `HandleShape`   | Transformations structurelles (reverse, repeat, pad, wrap, chunk, increment/decrement) |
| 8  | `HandleSplit`   | String → liste / liste → string                                                        |
| 9  | `HandleCompare` | Comparaison & similarité (utilise `CaseSensitivity` + `Ordering`)                      |
| 10 | `HandleFormat`  | Formatage printf-style + nombres (sans side-effect I/O)                                |
| 11 | `HandleEscape`  | Slashes & quotemeta                                                                    |
| 12 | `HandleEncode`  | Char codes / hex                                                                       |
| 13 | `HandleHash`    | Empreintes & phonétique                                                                |
| 14 | `HandleHtml`    | Entités & tags HTML                                                                    |

Aucune dépendance croisée entre traits ; le seul vocabulaire partagé est `Phyx\Enums\*`.

## API reference

Liste des méthodes publiques par trait, avec leur équivalent PHP natif. Pour chaque méthode, voir la PHPDoc inline du
fichier `src/Str/Handle*.php` correspondant pour la signature complète, les exemples annotés et les notes multibyte.

### `HandleSearch` — présence, position, sous-chaîne trouvée

| Phyx                                                  | PHP natif                     | Retour    |
|-------------------------------------------------------|-------------------------------|-----------|
| `Str::contains($value, $search, $case, $encoding)`    | `str_contains` / `mb_stripos` | `bool`    |
| `Str::startsWith($value, $search, $case, $encoding)`  | `str_starts_with`             | `bool`    |
| `Str::endsWith($value, $search, $case, $encoding)`    | `str_ends_with`               | `bool`    |
| `Str::indexOf($value, $search, $case, $encoding)`     | `mb_strpos` / `mb_stripos`    | `?int`    |
| `Str::lastIndexOf($value, $search, $case, $encoding)` | `mb_strrpos` / `mb_strripos`  | `?int`    |
| `Str::after($value, $search, $case, $encoding)`       | (dérivé)                      | `?string` |
| `Str::afterLast($value, $search, $case, $encoding)`   | (dérivé)                      | `?string` |
| `Str::before($value, $search, $case, $encoding)`      | (dérivé)                      | `?string` |
| `Str::beforeLast($value, $search, $case, $encoding)`  | (dérivé)                      | `?string` |
| `Str::firstOf($value, $search, $case, $encoding)`     | `mb_strstr` / `mb_stristr`    | `?string` |
| `Str::wordCount($value)`                              | `str_word_count`              | `int`     |
| `Str::occurrences($value, $search, $encoding)`        | `mb_substr_count`             | `int`     |
| `Str::span($value, $chars, $encoding)`                | `strspn` (multibyte)          | `int`     |
| `Str::cspan($value, $chars, $encoding)`               | `strcspn` (multibyte)         | `int`     |

### `HandleCase` — casse des lettres

| Phyx                                      | PHP natif                         | Retour   |
|-------------------------------------------|-----------------------------------|----------|
| `Str::lower($value, $encoding)`           | `mb_strtolower`                   | `string` |
| `Str::upper($value, $encoding)`           | `mb_strtoupper`                   | `string` |
| `Str::capitalize($value, $encoding)`      | `ucfirst` (multibyte)             | `string` |
| `Str::decapitalize($value, $encoding)`    | `lcfirst` (multibyte)             | `string` |
| `Str::capitalizeWords($value, $encoding)` | `mb_convert_case` (MB_CASE_TITLE) | `string` |

### `HandleTrim` — rognage des bords

| Phyx                               | PHP natif                              | Retour   |
|------------------------------------|----------------------------------------|----------|
| `Str::trim($value, $chars, $side)` | `trim` / `ltrim` / `rtrim` (multibyte) | `string` |

### `HandleLength` — mesure & inventaire

| Phyx                                | PHP natif                 | Retour               |
|-------------------------------------|---------------------------|----------------------|
| `Str::length($value, $encoding)`    | `mb_strlen`               | `int`                |
| `Str::charStats($value, $encoding)` | `count_chars` (multibyte) | `array<string, int>` |

### `HandleSlice` — extraction de portions

| Phyx                                                                   | PHP natif        | Retour         |
|------------------------------------------------------------------------|------------------|----------------|
| `Str::slice($value, $start, $length, $encoding)`                       | `mb_substr`      | `string`       |
| `Str::sliceCompare($value, $other, $start, $length, $case, $encoding)` | `substr_compare` | `int` (−1/0/1) |

### `HandleReplace` — remplacement

| Phyx                                                                  | PHP natif                      | Retour   |
|-----------------------------------------------------------------------|--------------------------------|----------|
| `Str::replace($value, $search, $replacement, $case, $encoding)`       | `str_replace` / `str_ireplace` | `string` |
| `Str::replaceMany($value, $replacements, $case, $encoding)`           | `str_replace` (array form)     | `string` |
| `Str::replaceSlice($value, $replacement, $start, $length, $encoding)` | `substr_replace` (multibyte)   | `string` |
| `Str::translate($value, $mapping)`                                    | `strtr`                        | `string` |
| `Str::rot13($value)`                                                  | `str_rot13`                    | `string` |

### `HandleShape` — transformations structurelles

| Phyx                                                          | PHP natif                                  | Retour   |
|---------------------------------------------------------------|--------------------------------------------|----------|
| `Str::reverse($value, $encoding)`                             | `strrev` (multibyte)                       | `string` |
| `Str::repeat($value, $times)`                                 | `str_repeat`                               | `string` |
| `Str::shuffle($value, $encoding)`                             | `str_shuffle` (multibyte)                  | `string` |
| `Str::pad($value, $length, $with, $side, $encoding)`          | `str_pad` (multibyte)                      | `string` |
| `Str::wrap($value, $width, $break, $cutLongWords, $encoding)` | `wordwrap` (multibyte)                     | `string` |
| `Str::chunk($value, $length, $separator, $encoding)`          | `chunk_split` (multibyte)                  | `string` |
| `Str::increment($value)`                                      | `str_increment` (PHP 8.3+, polyfill sinon) | `string` |
| `Str::decrement($value)`                                      | `str_decrement` (PHP 8.3+, polyfill sinon) | `string` |

### `HandleSplit` — split / join

| Phyx                                                | PHP natif            | Retour                    |
|-----------------------------------------------------|----------------------|---------------------------|
| `Str::split($value, $separator, $limit)`            | `explode`            | `list<string>`            |
| `Str::splitChars($value, $length, $encoding)`       | `mb_str_split`       | `list<string>`            |
| `Str::join($pieces, $separator)`                    | `implode`            | `string`                  |
| `Str::csv($value, $separator, $enclosure, $escape)` | `str_getcsv`         | `list<string>`            |
| `Str::scan($value, $format)`                        | `sscanf`             | `?array`                  |
| `Str::parseQuery($value)`                           | `parse_str` (safe)   | `array<array-key, mixed>` |
| `Str::tokenize($value, $separators)`                | `strtok` (stateless) | `list<string>`            |

### `HandleCompare` — comparaison & similarité

| Phyx                                                    | PHP natif                                                           | Retour                                |
|---------------------------------------------------------|---------------------------------------------------------------------|---------------------------------------|
| `Str::compare($a, $b, $case, $ordering, $encoding)`     | `strcmp` / `strcasecmp` / `strnatcmp` / `strnatcasecmp` / `strcoll` | `int` (−1/0/1)                        |
| `Str::comparePrefix($a, $b, $length, $case, $encoding)` | `strncmp` / `strncasecmp` (multibyte)                               | `int` (−1/0/1)                        |
| `Str::similarity($a, $b)`                               | `similar_text`                                                      | `array{matches: int, percent: float}` |
| `Str::distance($a, $b)`                                 | `levenshtein`                                                       | `int`                                 |

### `HandleFormat` — formatage

| Phyx                                                                | PHP natif       | Retour   |
|---------------------------------------------------------------------|-----------------|----------|
| `Str::format($template, ...$args)`                                  | `sprintf`       | `string` |
| `Str::formatArgs($template, $args)`                                 | `vsprintf`      | `string` |
| `Str::formatNumber($number, $decimals, $decimalSep, $thousandsSep)` | `number_format` | `string` |

### `HandleEscape` — slashes & quotemeta

| Phyx                                 | PHP natif       | Retour   |
|--------------------------------------|-----------------|----------|
| `Str::addSlashes($value)`            | `addslashes`    | `string` |
| `Str::addCSlashes($value, $charset)` | `addcslashes`   | `string` |
| `Str::stripSlashes($value)`          | `stripslashes`  | `string` |
| `Str::stripCSlashes($value)`         | `stripcslashes` | `string` |
| `Str::quoteMeta($value)`             | `quotemeta`     | `string` |

### `HandleEncode` — char codes & hex

| Phyx                                       | PHP natif                            | Retour   |
|--------------------------------------------|--------------------------------------|----------|
| `Str::toHex($value)`                       | `bin2hex`                            | `string` |
| `Str::fromHex($value)`                     | `hex2bin` (throw au lieu de `false`) | `string` |
| `Str::fromCharCode($codepoint, $encoding)` | `mb_chr`                             | `string` |
| `Str::toCharCode($value, $encoding)`       | `mb_ord`                             | `int`    |

### `HandleHash` — empreintes & phonétique

| Phyx                                | PHP natif                               | Retour             |
|-------------------------------------|-----------------------------------------|--------------------|
| `Str::md5($value)`                  | `md5`                                   | `string` (32 hex)  |
| `Str::md5File($path)`               | `md5_file` (null si fichier illisible)  | `?string`          |
| `Str::sha1($value)`                 | `sha1`                                  | `string` (40 hex)  |
| `Str::sha1File($path)`              | `sha1_file` (null si fichier illisible) | `?string`          |
| `Str::crc32($value)`                | `crc32`                                 | `int`              |
| `Str::crypt($value, $salt)`         | `crypt`                                 | `string`           |
| `Str::soundex($value)`              | `soundex`                               | `string` (4 chars) |
| `Str::metaphone($value, $phonemes)` | `metaphone`                             | `string`           |

### `HandleHtml` — entités & tags

| Phyx                                               | PHP natif                      | Retour                  |
|----------------------------------------------------|--------------------------------|-------------------------|
| `Str::escapeHtml($value, $flags, $encoding)`       | `htmlspecialchars`             | `string`                |
| `Str::unescapeHtml($value, $flags)`                | `htmlspecialchars_decode`      | `string`                |
| `Str::encodeEntities($value, $flags, $encoding)`   | `htmlentities`                 | `string`                |
| `Str::decodeEntities($value, $flags, $encoding)`   | `html_entity_decode`           | `string`                |
| `Str::translationTable($table, $flags, $encoding)` | `get_html_translation_table`   | `array<string, string>` |
| `Str::stripTags($value, $allowed)`                 | `strip_tags` (array form only) | `string`                |
| `Str::linebreaksToBr($value, $isXhtml)`            | `nl2br`                        | `string`                |

## Polyfills

Les fonctions PHP introduites après le minimum supporté (`^8.0`) sont polyfillées dans `src/Polyfills/` (namespace
`Phyx\Polyfills`) avec basculement runtime via `function_exists()` côté trait. Actuellement :

- `Phyx\Polyfills\AlphaIncrement` — polyfill de `str_increment` / `str_decrement` (PHP 8.3+).

## Tests

- 1 fichier de test par trait sous `tests/Str/` (miroir 1:1), 1 par polyfill sous `tests/Polyfills/`.
- **Cible 100 % lines + 100 % branches** sur `src/`, vérifié par `composer coverage:check`.
- Pour chaque méthode publique : nominal + edge (vide, 1 char, non trouvé) + multibyte + une assertion par valeur
  d'enum.
- Assertions strictes (`assertSame`), `#[DataProvider]` pour les branches, fixtures temp dans `setUp`/`tearDown` pour
  les variantes `*File`.
