# `Phyx\Json`

> Couche JSON de Phyx : une **façade statique** `Phyx\Json` qui encapsule `json_encode`, `json_decode`,
> `json_validate`, `json_last_error` et `JsonException` autour d'une API prédictible.
>
> 5 traits composés dans `Phyx\Json`, plusieurs enums dans `Phyx\Enums\`, ~30 méthodes publiques couvrant encodage,
> décodage typé, validation, erreurs et lecture simple de documents JSON.

## Principe directeur

L'API JSON native PHP est puissante mais historique : flags entiers, `null` ambigu sur decode, erreurs globales via
`json_last_error`, exceptions optionnelles. Phyx impose une règle simple : les méthodes principales jettent une
exception, les méthodes `try*` retournent `null`.

Le décodage doit être explicite : tableau associatif, objet, valeur scalaire ou structure libre.

## Conventions transversales

### Signature

- `public static`, `declare(strict_types=1)` partout.
- `JsonException` par défaut via `JSON_THROW_ON_ERROR`.
- Pas d'appel utilisateur à `json_last_error` nécessaire.
- `decodeArray` retourne toujours `array`.
- `decodeObject` retourne toujours `stdClass`.
- `tryDecode*` retourne `null` en cas d'erreur.
- Les flags natifs sont cachés derrière enums ou constantes Phyx quand possible.

### Retours prédictibles

| Cas                      | Retour                                             |
|--------------------------|----------------------------------------------------|
| Encodage valide          | `string`                                           |
| Décodage libre           | `array|object|string|int|float|bool|null`          |
| Décodage tableau         | `array`                                            |
| Décodage objet           | `stdClass`                                         |
| JSON invalide avec `try` | `null`                                             |
| Validation               | `bool`                                             |

### Vocabulaire fixe des paramètres

| Param      | Sens                              | Position       |
|------------|-----------------------------------|----------------|
| `$value`   | Valeur PHP à encoder              | 1er            |
| `$json`    | Chaîne JSON cible                 | 1er            |
| `$depth`   | Profondeur maximale               | avant flags    |
| `$flags`   | Options JSON                      | dernier        |
| `$path`    | Chemin d'accès dans document JSON | après `$json`  |

### Nommage des méthodes

| PHP natif            | Phyx              |
|----------------------|-------------------|
| `json_encode`        | `encode`          |
| `json_decode`        | `decode`          |
| `json_validate`      | `isValid`         |
| `json_last_error`    | `lastError`       |
| `json_last_error_msg`| `lastErrorMessage`|

## Enums (sous `Phyx\Enums\`)

| Enum         | Valeurs                                      | Utilisé par |
|--------------|----------------------------------------------|-------------|
| `JsonShape`  | `Array`, `Object`, `Value`                   | `decodeAs`  |
| `JsonOutput` | `Compact`, `Pretty`, `UnescapedUnicode`, `UnescapedSlashes` | `encode` |
| `JsonErrorMode` | `Throw`, `Null`                           | méthodes internes |
| `JsonPathMode` | `Null`, `Throw`, `Default`                 | `get` |

## Traits (sous `Phyx\Json\`)

| # | Trait            | Responsabilité                    |
|---|------------------|-----------------------------------|
| 1 | `HandleEncode`   | Encodage JSON                     |
| 2 | `HandleDecode`   | Décodage typé                     |
| 3 | `HandleValidate` | Validation                        |
| 4 | `HandleError`    | Erreurs natives                   |
| 5 | `HandleAccess`   | Accès simple à un document décodé |

## API reference

### `HandleEncode` - encodage

| Phyx                              | PHP natif     | Retour |
|-----------------------------------|---------------|--------|
| `Json::encode($value, $flags, $depth)` | `json_encode` | `string` |
| `Json::pretty($value, $flags, $depth)` | `json_encode` | `string` |
| `Json::tryEncode($value, $flags, $depth)` | `json_encode` | `?string` |

### `HandleDecode` - décodage

| Phyx                                  | PHP natif     | Retour |
|---------------------------------------|---------------|--------|
| `Json::decode($json, $depth, $flags)` | `json_decode` | `array|object|string|int|float|bool|null` |
| `Json::decodeArray($json, $depth, $flags)` | `json_decode` | `array` |
| `Json::decodeObject($json, $depth, $flags)` | `json_decode` | `stdClass` |
| `Json::tryDecode($json, $depth, $flags)` | `json_decode` | `mixed|null` |
| `Json::tryDecodeArray($json, $depth, $flags)` | `json_decode` | `?array` |

### `HandleValidate` - validation

| Phyx                         | PHP natif / logique | Retour |
|------------------------------|---------------------|--------|
| `Json::isValid($json, $depth)` | `json_validate` ou decode safe | `bool` |
| `Json::assertValid($json, $depth)` | `json_validate` ou decode safe | `void` |

### `HandleError` - erreurs

| Phyx                         | PHP natif              | Retour |
|------------------------------|------------------------|--------|
| `Json::lastError()`          | `json_last_error`      | `int` |
| `Json::lastErrorMessage()`   | `json_last_error_msg`  | `string` |

### `HandleAccess` - accès à un document JSON

| Phyx                                      | PHP natif / logique | Retour |
|-------------------------------------------|---------------------|--------|
| `Json::get($json, $path, $default)`       | `json_decode` + Arr | `mixed` |
| `Json::has($json, $path)`                 | `json_decode` + Arr | `bool` |
| `Json::set($json, $path, $value)`         | decode + encode     | `string` |
| `Json::remove($json, $path)`              | decode + encode     | `string` |

## Polyfills

- `json_validate` est disponible depuis PHP 8.3. Si le minimum supporté est `^8.0`, prévoir un polyfill interne basé sur
  `json_decode` avec `JSON_THROW_ON_ERROR`.

## Exclusions

- JSON Schema : domaine séparé `JsonSchema`.
- JSONPath complet : domaine séparé si besoin.
- Sérialisation d'objets métier : domaine `Serializer`.

## Tests

- 1 fichier de test par trait sous `tests/Json/`.
- Cas obligatoires : JSON invalide, `null` JSON valide, objet, tableau, scalaire, profondeur dépassée, Unicode,
  slashs, pretty print.
- Tests séparés pour `decode`, `decodeArray`, `decodeObject` afin d'éviter l'ambiguïté du `null` natif.
