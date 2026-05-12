# `Phyx\Num`

> Couche numérique de Phyx : une **façade statique** `Phyx\Num` qui réécrit les fonctions mathématiques natives PHP
> autour d'une API claire pour entiers, flottants, bornes, arrondis, formatage et conversions de base.
>
> 9 traits composés dans `Phyx\Num`, plusieurs enums dans `Phyx\Enums\`, ~60 méthodes publiques couvrant les usages
> quotidiens : bornage, comparaison, arrondi, agrégation, trigonométrie, puissances, conversions, aléatoire et formatage.

## Principe directeur

PHP mélange fonctions numériques, mathématiques, formatage d'affichage et conversions de base. Phyx sépare les intentions
tout en gardant une façade unique `Num` pour les opérations qui manipulent directement un nombre.

La règle : si l'opération retourne ou consomme principalement un nombre, elle va dans `Num`. Si elle concerne une monnaie,
une précision décimale arbitraire ou une localisation avancée, elle doit partir dans un domaine spécialisé plus tard
(`Money`, `Decimal`, `Locale`).

## Conventions transversales

### Signature

- `public static`, `declare(strict_types=1)` partout.
- Signatures explicites : `int|float` uniquement quand la méthode accepte réellement les deux.
- Pas de retour `false`.
- Les divisions invalides jettent une exception explicite.
- Les méthodes qui formatent retournent `string`, les méthodes qui calculent retournent `int|float`.

### Retours prédictibles

| Cas                         | Retour                  |
|-----------------------------|-------------------------|
| Prédicat numérique          | `bool`                  |
| Bornage / calcul            | `int|float`             |
| Arrondi                     | `float` ou `int` selon méthode |
| Agrégation vide             | `null` ou exception selon méthode |
| Formatage                   | `string`                |
| Conversion impossible       | exception               |

### Vocabulaire fixe des paramètres

| Param          | Sens                              | Position               |
|----------------|-----------------------------------|------------------------|
| `$value`       | Nombre cible                      | Toujours 1er           |
| `$min`         | Borne minimale                    | après `$value`         |
| `$max`         | Borne maximale                    | après `$min`           |
| `$precision`   | Nombre de décimales               | selon méthode          |
| `$mode`        | Mode d'arrondi ou stratégie       | dernier                |
| `$base`        | Base numérique                    | selon conversion       |
| `$numbers`     | Liste de nombres                  | 1er pour agrégations   |
| `$decimals`    | Décimales affichées               | formatage              |

### Nommage des méthodes

| PHP natif                    | Phyx             |
|------------------------------|------------------|
| `abs`                        | `abs`            |
| `min` / `max`                | `min` / `max`    |
| `round`                      | `round`          |
| `ceil`                       | `ceil`           |
| `floor`                      | `floor`          |
| `intdiv`                     | `divideInt`      |
| `fmod`                       | `mod`            |
| `pow`                        | `power`          |
| `sqrt`                       | `sqrt`           |
| `number_format`              | `format`         |
| `is_nan`                     | `isNan`          |
| `is_finite`                  | `isFinite`       |
| `base_convert`               | `convertBase`    |
| `decbin` / `dechex` / `decoct`| `toBase`         |
| `bindec` / `hexdec` / `octdec`| `fromBase`       |

### PHPDoc obligatoire

Chaque méthode publique porte un bloc PHPDoc complet :

- 1ere ligne impérative courte (< 100 chars).
- Paragraphe décrivant précision, arrondi, exceptions et divergence avec le natif.
- Un `@param` par argument.
- `@return` toujours présent.
- `@throws` pour division par zéro, base invalide, format impossible.
- `@example` annoté (`// =>` pour les résultats).
- `@see` vers enums liés + fonction PHP native équivalente.

## Enums (sous `Phyx\Enums\`)

| Enum            | Valeurs                                               | Utilisé par             |
|-----------------|-------------------------------------------------------|-------------------------|
| `Rounding`      | `HalfUp`, `HalfDown`, `HalfEven`, `HalfOdd`, `TowardZero`, `AwayFromZero` | `round` |
| `Boundary`      | `Inclusive`, `Exclusive`                              | `between`               |
| `NumberFormat`  | `Default`, `Localized`, `Compact`                     | `format`                |
| `AngleUnit`     | `Degrees`, `Radians`                                  | trigonométrie           |
| `RandomMode`    | `Pseudo`, `Secure`                                    | `randomInt`, `randomFloat` |

## Traits (sous `Phyx\Num\`)

| # | Trait             | Responsabilité                              |
|---|-------------------|---------------------------------------------|
| 1 | `HandleCheck`     | Prédicats numériques                        |
| 2 | `HandleRange`     | Bornes, clamp, between                      |
| 3 | `HandleRound`     | Arrondis, ceil, floor, trunc                |
| 4 | `HandleArithmetic`| Opérations simples, division, modulo        |
| 5 | `HandleAggregate` | Min, max, sum, average, median              |
| 6 | `HandlePower`     | Puissances, racines, logs, exponentielles   |
| 7 | `HandleTrig`      | Trigonométrie                               |
| 8 | `HandleConvert`   | Bases, pourcentages, ratios                 |
| 9 | `HandleFormat`    | Formatage d'affichage                       |

## API reference

### `HandleCheck` - prédicats numériques

| Phyx                     | PHP natif      | Retour |
|--------------------------|----------------|--------|
| `Num::isEven($value)`    | dérivé         | `bool` |
| `Num::isOdd($value)`     | dérivé         | `bool` |
| `Num::isPositive($value)`| dérivé         | `bool` |
| `Num::isNegative($value)`| dérivé         | `bool` |
| `Num::isZero($value)`    | dérivé         | `bool` |
| `Num::isFinite($value)`  | `is_finite`    | `bool` |
| `Num::isInfinite($value)`| `is_infinite`  | `bool` |
| `Num::isNan($value)`     | `is_nan`       | `bool` |

### `HandleRange` - bornes

| Phyx                                          | PHP natif / logique | Retour      |
|-----------------------------------------------|---------------------|-------------|
| `Num::clamp($value, $min, $max)`              | `min` / `max`       | `int|float` |
| `Num::between($value, $min, $max, $boundary)` | dérivé              | `bool`      |
| `Num::outside($value, $min, $max, $boundary)` | dérivé              | `bool`      |
| `Num::normalize($value, $min, $max)`          | dérivé              | `float`     |

### `HandleRound` - arrondis

| Phyx                                 | PHP natif  | Retour      |
|--------------------------------------|------------|-------------|
| `Num::round($value, $precision, $mode)` | `round` | `float`     |
| `Num::ceil($value)`                  | `ceil`     | `float`     |
| `Num::floor($value)`                 | `floor`    | `float`     |
| `Num::truncate($value)`              | dérivé     | `int`       |

### `HandleArithmetic` - opérations simples

| Phyx                                   | PHP natif | Retour      |
|----------------------------------------|-----------|-------------|
| `Num::abs($value)`                     | `abs`     | `int|float` |
| `Num::mod($value, $divisor)`           | `fmod`    | `float`     |
| `Num::divideInt($value, $divisor)`     | `intdiv`  | `int`       |
| `Num::sign($value)`                    | dérivé    | `int`       |
| `Num::percentageOf($value, $total)`    | dérivé    | `float`     |

### `HandleAggregate` - agrégations

| Phyx                         | PHP natif / logique | Retour      |
|------------------------------|---------------------|-------------|
| `Num::min($numbers)`         | `min`               | `int|float|null` |
| `Num::max($numbers)`         | `max`               | `int|float|null` |
| `Num::sum($numbers)`         | `array_sum`         | `int|float` |
| `Num::average($numbers)`     | dérivé              | `?float`    |
| `Num::median($numbers)`      | dérivé              | `?float`    |

### `HandlePower` - puissances et racines

| Phyx                         | PHP natif | Retour |
|------------------------------|-----------|--------|
| `Num::power($value, $exponent)` | `pow`  | `int|float` |
| `Num::sqrt($value)`          | `sqrt`    | `float` |
| `Num::log($value, $base)`    | `log`     | `float` |
| `Num::exp($value)`           | `exp`     | `float` |

### `HandleTrig` - trigonométrie

| Phyx                             | PHP natif | Retour |
|----------------------------------|-----------|--------|
| `Num::sin($value, $unit)`        | `sin`     | `float` |
| `Num::cos($value, $unit)`        | `cos`     | `float` |
| `Num::tan($value, $unit)`        | `tan`     | `float` |
| `Num::asin($value, $unit)`       | `asin`    | `float` |
| `Num::acos($value, $unit)`       | `acos`    | `float` |
| `Num::atan($value, $unit)`       | `atan`    | `float` |
| `Num::toRadians($degrees)`       | `deg2rad` | `float` |
| `Num::toDegrees($radians)`       | `rad2deg` | `float` |

### `HandleConvert` - bases et ratios

| Phyx                                      | PHP natif       | Retour |
|-------------------------------------------|-----------------|--------|
| `Num::convertBase($value, $from, $to)`    | `base_convert`  | `string` |
| `Num::toBase($value, $base)`              | `decbin` / `dechex` / `decoct` | `string` |
| `Num::fromBase($value, $base)`            | `bindec` / `hexdec` / `octdec` | `int|float` |
| `Num::ratio($value, $total)`              | dérivé          | `float` |
| `Num::percentage($value, $total)`         | dérivé          | `float` |

### `HandleFormat` - formatage

| Phyx                                                         | PHP natif       | Retour |
|--------------------------------------------------------------|-----------------|--------|
| `Num::format($value, $decimals, $decimalSep, $thousandsSep)` | `number_format` | `string` |
| `Num::toString($value)`                                      | cast contrôlé   | `string` |
| `Num::ordinal($value, $locale)`                              | `NumberFormatter` si disponible | `string` |

## Polyfills

- Polyfill possible pour les modes d'arrondi non disponibles selon la version PHP minimale.
- `random_int` est natif depuis PHP 7 ; aucune polyfill nécessaire si le minimum est `^8.0`.

## Tests

- 1 fichier de test par trait sous `tests/Num/`.
- Cas obligatoires : zéro, nombres négatifs, floats, `INF`, `NAN`, divisions par zéro, bornes inclusives/exclusives.
- Les tests d'arrondi doivent couvrir toutes les valeurs de `Rounding`.
- Les conversions de base doivent tester base 2, 8, 10, 16, 36.
