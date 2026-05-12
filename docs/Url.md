# `Phyx\Url`

> Couche URL de Phyx : une **façade statique** `Phyx\Url` pour parser, valider, construire et modifier des URLs sans
> exposer les bizarreries de `parse_url`, `parse_str`, `http_build_query`, `urlencode` et `rawurlencode`.
>
> 8 traits composés dans `Phyx\Url`, plusieurs enums dans `Phyx\Enums\`, ~60 méthodes publiques couvrant parsing,
> composants, query string, encodage, validation, normalisation et construction.

## Principe directeur

Une URL n'est pas une string ordinaire. Phyx doit séparer les composants (`scheme`, `host`, `path`, `query`,
`fragment`) et éviter les comportements ambigus des fonctions natives qui retournent parfois `false`, `null` ou des
tableaux partiels.

`Url` reste un module syntaxique : il ne fait pas de requête HTTP.

## Conventions transversales

### Signature

- `public static`, `declare(strict_types=1)` partout.
- `parse` retourne une structure stable, jamais `false`.
- Les composants absents retournent `null`.
- Les méthodes `with*` retournent une URL reconstruite.
- Les méthodes de query retournent `array<string, mixed>` ou `string` selon le sens de conversion.
- Encodage RFC clair via enum, pas de choix implicite entre `urlencode` et `rawurlencode`.

### Retours prédictibles

| Cas                    | Retour                       |
|------------------------|------------------------------|
| URL valide             | `bool`                       |
| Composant absent       | `null`                       |
| Composants parsés      | `array<string, mixed>`       |
| URL reconstruite       | `string`                     |
| Query parsée           | `array<string, mixed>`       |

### Vocabulaire fixe des paramètres

| Param       | Sens                         | Position      |
|-------------|------------------------------|---------------|
| `$url`      | URL cible                    | Toujours 1er  |
| `$query`    | Query string ou tableau      | selon méthode |
| `$key`      | Clé de query                 | après `$url`  |
| `$value`    | Valeur de query              | après `$key`  |
| `$component`| Composant d'URL              | selon méthode |
| `$encoding` | Mode d'encodage URL          | dernier       |

### Nommage des méthodes

| PHP natif          | Phyx              |
|--------------------|-------------------|
| `parse_url`        | `parse`           |
| `http_build_query` | `buildQuery`      |
| `parse_str`        | `parseQuery`      |
| `urlencode`        | `encode`          |
| `urldecode`        | `decode`          |
| `rawurlencode`     | `encodeComponent` |
| `rawurldecode`     | `decodeComponent` |
| `filter_var`       | `isValid`         |

## Enums (sous `Phyx\Enums\`)

| Enum           | Valeurs                          | Utilisé par               |
|----------------|----------------------------------|---------------------------|
| `UrlComponent` | `Scheme`, `User`, `Pass`, `Host`, `Port`, `Path`, `Query`, `Fragment` | `component`, `without` |
| `UrlEncoding`  | `Form`, `Rfc3986`                | `encode`, `buildQuery`    |
| `QueryFormat`  | `Rfc1738`, `Rfc3986`             | `buildQuery`              |
| `UrlValidation`| `Absolute`, `Relative`, `Http`   | `isValid`                 |

## Traits (sous `Phyx\Url\`)

| # | Trait           | Responsabilité                              |
|---|-----------------|---------------------------------------------|
| 1 | `HandleParse`   | Parsing stable                              |
| 2 | `HandleParts`   | Accès aux composants                        |
| 3 | `HandleBuild`   | Reconstruction et mutation de composants    |
| 4 | `HandleQuery`   | Query string                                |
| 5 | `HandleEncode`  | Encodage / décodage                         |
| 6 | `HandleValidate`| Validation syntaxique                       |
| 7 | `HandleNormalize` | Normalisation                             |
| 8 | `HandleCompare` | Comparaison logique                         |

## API reference

### `HandleParse` - parsing

| Phyx                  | PHP natif   | Retour |
|-----------------------|-------------|--------|
| `Url::parse($url)`    | `parse_url` | `array<string, mixed>` |
| `Url::tryParse($url)` | `parse_url` | `?array` |
| `Url::component($url, $component)` | `parse_url` | `string|int|null` |

### `HandleParts` - composants

| Phyx                    | PHP natif / logique | Retour |
|-------------------------|---------------------|--------|
| `Url::scheme($url)`     | `parse_url`         | `?string` |
| `Url::user($url)`       | `parse_url`         | `?string` |
| `Url::password($url)`   | `parse_url`         | `?string` |
| `Url::host($url)`       | `parse_url`         | `?string` |
| `Url::port($url)`       | `parse_url`         | `?int` |
| `Url::path($url)`       | `parse_url`         | `?string` |
| `Url::query($url)`      | `parse_url`         | `?string` |
| `Url::fragment($url)`   | `parse_url`         | `?string` |

### `HandleBuild` - reconstruction

| Phyx                              | PHP natif / logique | Retour |
|-----------------------------------|---------------------|--------|
| `Url::build($parts)`              | dérivé              | `string` |
| `Url::withScheme($url, $scheme)`  | dérivé              | `string` |
| `Url::withHost($url, $host)`      | dérivé              | `string` |
| `Url::withPath($url, $path)`      | dérivé              | `string` |
| `Url::withFragment($url, $fragment)` | dérivé           | `string` |
| `Url::withoutFragment($url)`      | dérivé              | `string` |

### `HandleQuery` - query string

| Phyx                                      | PHP natif            | Retour |
|-------------------------------------------|----------------------|--------|
| `Url::parseQuery($query)`                 | `parse_str` safe     | `array<string, mixed>` |
| `Url::buildQuery($parameters, $format)`   | `http_build_query`   | `string` |
| `Url::queryParameters($url)`              | `parse_str` safe     | `array<string, mixed>` |
| `Url::withQuery($url, $parameters)`       | `http_build_query`   | `string` |
| `Url::withQueryValue($url, $key, $value)` | dérivé               | `string` |
| `Url::withoutQueryValue($url, $key)`      | dérivé               | `string` |

### `HandleEncode` - encodage

| Phyx                            | PHP natif       | Retour |
|---------------------------------|-----------------|--------|
| `Url::encode($value, $encoding)`| `urlencode` / `rawurlencode` | `string` |
| `Url::decode($value, $encoding)`| `urldecode` / `rawurldecode` | `string` |
| `Url::encodeComponent($value)`  | `rawurlencode`  | `string` |
| `Url::decodeComponent($value)`  | `rawurldecode`  | `string` |

### `HandleValidate` - validation

| Phyx                             | PHP natif / logique | Retour |
|----------------------------------|---------------------|--------|
| `Url::isValid($url, $validation)`| `filter_var`        | `bool` |
| `Url::isAbsolute($url)`          | dérivé              | `bool` |
| `Url::isRelative($url)`          | dérivé              | `bool` |
| `Url::isHttp($url)`              | dérivé              | `bool` |
| `Url::isHttps($url)`             | dérivé              | `bool` |

### `HandleNormalize` - normalisation

| Phyx                         | PHP natif / logique | Retour |
|------------------------------|---------------------|--------|
| `Url::normalize($url)`       | dérivé              | `string` |
| `Url::removeDefaultPort($url)` | dérivé            | `string` |
| `Url::sortQuery($url)`       | dérivé              | `string` |

### `HandleCompare` - comparaison

| Phyx                         | PHP natif / logique | Retour |
|------------------------------|---------------------|--------|
| `Url::sameOrigin($a, $b)`    | dérivé              | `bool` |
| `Url::sameHost($a, $b)`      | dérivé              | `bool` |
| `Url::samePath($a, $b)`      | dérivé              | `bool` |

## Exclusions

- Requêtes HTTP, headers, status codes : domaine `Http`.
- Chemins fichiers : domaine `Path`.
- HTML links et DOM : domaine `Html`.

## Tests

- 1 fichier de test par trait sous `tests/Url/`.
- Cas obligatoires : URL absolue, relative, IPv6, port, auth, query vide, fragment vide, encodage RFC1738/RFC3986.
- `parse_url` doit être encapsulé pour éviter les retours `false` exposés à l'utilisateur.
