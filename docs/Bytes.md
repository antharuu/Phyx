# `Phyx\Bytes`

> Couche bytes de Phyx : une **façade statique** `Phyx\Bytes` pour manipuler une chaîne PHP comme une suite d'octets,
> et non comme du texte multibyte.
>
> 8 traits composés dans `Phyx\Bytes`, plusieurs enums dans `Phyx\Enums\`, ~50 méthodes publiques couvrant longueur
> binaire, slices, hex, base64, codes d'octets, packing, random bytes, checksum léger et inspection.

## Principe directeur

En PHP, une string peut représenter du texte ou des données binaires. `Str` traite le texte lisible. `Bytes` traite les
octets bruts. Aucune méthode `Bytes` ne doit utiliser `mb_*`.

Cette séparation évite les confusions entre longueur en caractères et longueur en octets, entre `mb_substr` et `substr`,
entre Unicode et binaire.

## Conventions transversales

### Signature

- `public static`, `declare(strict_types=1)` partout.
- Aucun `Encoding $encoding` dans `Bytes`.
- Les positions et longueurs sont en octets.
- Les retours impossibles sont `null` ou exception, jamais `false`.
- Les méthodes de décodage strictes jettent une exception si l'entrée est invalide.
- Les méthodes `try*` retournent `null`.

### Retours prédictibles

| Cas                         | Retour             |
|-----------------------------|--------------------|
| Longueur / position         | `int`              |
| Transformation binaire      | `string`           |
| Décodage invalide strict    | exception          |
| Décodage invalide try       | `null`             |
| Split binaire               | `list<string>`     |
| Prédicat                    | `bool`             |

### Vocabulaire fixe des paramètres

| Param        | Sens                            | Position      |
|--------------|---------------------------------|---------------|
| `$bytes`     | Données binaires cible          | Toujours 1er  |
| `$offset`    | Offset en octets                | selon méthode |
| `$length`    | Longueur en octets              | selon méthode |
| `$value`     | Valeur encodée                  | selon méthode |
| `$byte`      | Octet individuel                | selon méthode |
| `$format`    | Format `pack` / `unpack`        | 1er ou 2e     |

### Nommage des méthodes

| PHP natif          | Phyx         |
|--------------------|--------------|
| `strlen`           | `length`     |
| `substr`           | `slice`      |
| `bin2hex`          | `toHex`      |
| `hex2bin`          | `fromHex`    |
| `ord`              | `toInt`      |
| `chr`              | `fromInt`    |
| `str_split`        | `split`      |
| `chunk_split`      | `chunk`      |
| `base64_encode`    | `toBase64`   |
| `base64_decode`    | `fromBase64` |
| `pack`             | `pack`       |
| `unpack`           | `unpack`     |
| `random_bytes`     | `random`     |

## Enums (sous `Phyx\Enums\`)

| Enum             | Valeurs                              | Utilisé par |
|------------------|--------------------------------------|-------------|
| `ByteOrder`      | `BigEndian`, `LittleEndian`, `Machine` | `pack`, `unpack` |
| `BinaryEncoding` | `Hex`, `Base64`, `Base64Url`          | `encode`, `decode` |
| `PaddingMode`    | `Strict`, `Lenient`                   | `fromBase64` |
| `Checksum`       | `Crc32`, `Adler32`                    | `checksum` |

## Traits (sous `Phyx\Bytes\`)

| # | Trait           | Responsabilité                         |
|---|-----------------|----------------------------------------|
| 1 | `HandleLength`  | Taille et vide                         |
| 2 | `HandleSlice`   | Extraction binaire                     |
| 3 | `HandleSplit`   | Split et chunks                        |
| 4 | `HandleHex`     | Hex encode/decode                      |
| 5 | `HandleBase64`  | Base64 et base64url                    |
| 6 | `HandleByte`    | `ord`, `chr`, octets individuels       |
| 7 | `HandlePack`    | `pack` / `unpack`                      |
| 8 | `HandleRandom`  | Random bytes et checksum léger         |

## API reference

### `HandleLength` - taille

| Phyx                         | PHP natif | Retour |
|------------------------------|-----------|--------|
| `Bytes::length($bytes)`      | `strlen`  | `int` |
| `Bytes::isEmpty($bytes)`     | `strlen`  | `bool` |
| `Bytes::isNotEmpty($bytes)`  | `strlen`  | `bool` |

### `HandleSlice` - extraction

| Phyx                                      | PHP natif | Retour |
|-------------------------------------------|-----------|--------|
| `Bytes::slice($bytes, $offset, $length)`  | `substr`  | `string` |
| `Bytes::take($bytes, $length)`            | `substr`  | `string` |
| `Bytes::takeLast($bytes, $length)`        | `substr`  | `string` |
| `Bytes::byteAt($bytes, $offset)`          | `substr`  | `?string` |

### `HandleSplit` - split et chunks

| Phyx                                      | PHP natif      | Retour |
|-------------------------------------------|----------------|--------|
| `Bytes::split($bytes, $length)`           | `str_split`    | `list<string>` |
| `Bytes::chunk($bytes, $length, $separator)` | `chunk_split` | `string` |

### `HandleHex` - hex

| Phyx                       | PHP natif | Retour |
|----------------------------|-----------|--------|
| `Bytes::toHex($bytes)`     | `bin2hex` | `string` |
| `Bytes::fromHex($value)`   | `hex2bin` | `string` |
| `Bytes::tryFromHex($value)`| `hex2bin` | `?string` |
| `Bytes::isHex($value)`     | dérivé    | `bool` |

### `HandleBase64` - base64

| Phyx                              | PHP natif        | Retour |
|-----------------------------------|------------------|--------|
| `Bytes::toBase64($bytes)`         | `base64_encode`  | `string` |
| `Bytes::fromBase64($value, $mode)`| `base64_decode`  | `string` |
| `Bytes::tryFromBase64($value, $mode)` | `base64_decode` | `?string` |
| `Bytes::toBase64Url($bytes)`      | dérivé           | `string` |
| `Bytes::fromBase64Url($value)`    | dérivé           | `string` |

### `HandleByte` - octet individuel

| Phyx                              | PHP natif | Retour |
|-----------------------------------|-----------|--------|
| `Bytes::fromInt($byte)`           | `chr`     | `string` |
| `Bytes::toInt($byte)`             | `ord`     | `int` |
| `Bytes::ints($bytes)`             | `ord` loop| `list<int>` |
| `Bytes::fromInts($bytes)`         | `chr` loop| `string` |

### `HandlePack` - pack / unpack

| Phyx                              | PHP natif | Retour |
|-----------------------------------|-----------|--------|
| `Bytes::pack($format, ...$values)`| `pack`    | `string` |
| `Bytes::unpack($format, $bytes)`  | `unpack`  | `array<string|int, mixed>` |

### `HandleRandom` - random et checksum

| Phyx                              | PHP natif       | Retour |
|-----------------------------------|-----------------|--------|
| `Bytes::random($length)`          | `random_bytes`  | `string` |
| `Bytes::checksum($bytes, $algorithm)` | `hash` / `crc32` | `string|int` |
| `Bytes::crc32($bytes)`            | `crc32`         | `int` |

## Exclusions

- Texte multibyte : domaine `Str`.
- Hash cryptographique complet : domaine `Hash` ou `Crypto`.
- Encodage de caractères (`UTF-8`, `ISO-8859-1`) : domaine `Encoding`.
- Fichiers binaires : domaine `File`.

## Tests

- 1 fichier de test par trait sous `tests/Bytes/`.
- Cas obligatoires : chaîne vide, octet `\0`, octets non imprimables, données UTF-8 traitées comme bytes, hex invalide,
  base64 invalide, offsets hors limites.
- Les tests doivent vérifier que `Bytes::length('é')` vaut la taille en octets, pas le nombre de caractères.
