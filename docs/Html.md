# `Phyx\Html`

> Couche HTML de Phyx : une **façade statique** `Phyx\Html` dédiée à l'échappement, aux entités, aux attributs,
> aux tags et aux transformations texte-vers-HTML.
>
> 7 traits composés dans `Phyx\Html`, plusieurs enums dans `Phyx\Enums\`, ~45 méthodes publiques couvrant
> `htmlspecialchars`, `htmlentities`, `strip_tags`, `nl2br`, tables d'entités et helpers HTML sûrs.

## Principe directeur

L'échappement HTML ne doit pas être caché dans `Str`. Le domaine HTML impose un contexte : texte, attribut, entités,
tag, fragment. Phyx doit rendre ce contexte explicite pour éviter les confusions entre affichage, nettoyage et
conversion.

`Html::escape` doit être sûr par défaut. Les options dangereuses ou ambiguës doivent être nommées.

## Conventions transversales

### Signature

- `public static`, `declare(strict_types=1)` partout.
- Encodage via `Encoding $encoding = Encoding::Utf8`.
- Les flags HTML passent par enum ou valeur contrôlée, pas par entier magique dans l'API haut niveau.
- `stripTags` accepte uniquement une `list<string>` de tags autorisés, pas une string HTML brute.
- Les méthodes retournent toujours une string ou un tableau typé.

### Retours prédictibles

| Cas                    | Retour                  |
|------------------------|-------------------------|
| Echappement            | `string`                |
| Décodage               | `string`                |
| Nettoyage de tags      | `string`                |
| Table d'entités        | `array<string, string>` |
| Attributs assemblés    | `string`                |

### Vocabulaire fixe des paramètres

| Param        | Sens                               | Position       |
|--------------|------------------------------------|----------------|
| `$value`     | Texte ou fragment cible            | Toujours 1er   |
| `$attributes`| Attributs HTML                    | selon méthode  |
| `$allowed`   | Tags autorisés                     | après `$value` |
| `$flags`     | Flags HTML                         | avant encodage |
| `$encoding`  | Encodage                           | dernier        |

### Nommage des méthodes

| PHP natif                    | Phyx              |
|------------------------------|-------------------|
| `htmlspecialchars`           | `escape`          |
| `htmlspecialchars_decode`    | `unescape`        |
| `htmlentities`               | `encodeEntities`  |
| `html_entity_decode`         | `decodeEntities`  |
| `get_html_translation_table` | `translationTable`|
| `strip_tags`                 | `stripTags`       |
| `nl2br`                      | `linebreaksToBr`  |

## Enums (sous `Phyx\Enums\`)

| Enum             | Valeurs                                      | Utilisé par |
|------------------|----------------------------------------------|-------------|
| `HtmlContext`    | `Text`, `Attribute`, `Url`, `Raw`            | `escapeFor` |
| `HtmlQuotes`     | `Double`, `Single`, `Both`, `None`           | `escape`    |
| `HtmlDoctype`    | `Html5`, `Xhtml`                             | `linebreaksToBr` |
| `HtmlTable`      | `SpecialChars`, `Entities`                   | `translationTable` |
| `Encoding`       | `Utf8`, `Iso88591`, `Windows1252`, ...       | toutes les méthodes encodées |

## Traits (sous `Phyx\Html\`)

| # | Trait             | Responsabilité                          |
|---|-------------------|-----------------------------------------|
| 1 | `HandleEscape`    | Echappement HTML                        |
| 2 | `HandleEntities`  | Entités HTML                            |
| 3 | `HandleTags`      | Suppression et validation de tags       |
| 4 | `HandleAttributes`| Construction d'attributs                |
| 5 | `HandleText`      | Transformations texte-vers-HTML         |
| 6 | `HandleFragments` | Fragments et petits helpers de balises  |
| 7 | `HandleTables`    | Tables de traduction                    |

## API reference

### `HandleEscape` - échappement

| Phyx                                      | PHP natif                 | Retour |
|-------------------------------------------|---------------------------|--------|
| `Html::escape($value, $flags, $encoding)` | `htmlspecialchars`        | `string` |
| `Html::unescape($value, $flags)`          | `htmlspecialchars_decode` | `string` |
| `Html::escapeAttribute($value, $encoding)`| `htmlspecialchars`        | `string` |
| `Html::escapeFor($value, $context, $encoding)` | dérivé              | `string` |

### `HandleEntities` - entités HTML

| Phyx                                               | PHP natif            | Retour |
|----------------------------------------------------|----------------------|--------|
| `Html::encodeEntities($value, $flags, $encoding)`  | `htmlentities`       | `string` |
| `Html::decodeEntities($value, $flags, $encoding)`  | `html_entity_decode` | `string` |

### `HandleTags` - tags

| Phyx                                      | PHP natif / logique | Retour |
|-------------------------------------------|---------------------|--------|
| `Html::stripTags($value, $allowed)`       | `strip_tags`        | `string` |
| `Html::hasTags($value)`                   | dérivé              | `bool` |
| `Html::allowedTags($allowed)`             | dérivé              | `string` interne |

### `HandleAttributes` - attributs

| Phyx                                      | PHP natif / logique | Retour |
|-------------------------------------------|---------------------|--------|
| `Html::attributes($attributes)`           | dérivé              | `string` |
| `Html::attribute($name, $value)`          | dérivé              | `string` |
| `Html::booleanAttribute($name, $enabled)` | dérivé              | `string` |

### `HandleText` - texte vers HTML

| Phyx                                      | PHP natif / logique | Retour |
|-------------------------------------------|---------------------|--------|
| `Html::linebreaksToBr($value, $doctype)`  | `nl2br`             | `string` |
| `Html::paragraphs($value)`                | dérivé              | `string` |
| `Html::text($value, $encoding)`           | `htmlspecialchars`  | `string` |

### `HandleFragments` - fragments

| Phyx                                      | PHP natif / logique | Retour |
|-------------------------------------------|---------------------|--------|
| `Html::tag($name, $content, $attributes)` | dérivé              | `string` |
| `Html::voidTag($name, $attributes)`       | dérivé              | `string` |
| `Html::comment($value)`                   | dérivé              | `string` |

### `HandleTables` - tables

| Phyx                                               | PHP natif                    | Retour |
|----------------------------------------------------|------------------------------|--------|
| `Html::translationTable($table, $flags, $encoding)`| `get_html_translation_table` | `array<string, string>` |

## Exclusions

- Parsing DOM complet : domaine `Dom` éventuel.
- Markdown : domaine `Markdown`.
- Sanitization HTML robuste contre XSS : dépend d'une librairie dédiée, ne pas promettre plus que `stripTags`.

## Tests

- 1 fichier de test par trait sous `tests/Html/`.
- Cas obligatoires : guillemets simples/doubles, UTF-8, entités existantes, tags autorisés, attributs booléens,
  valeurs `null` dans les attributs.
- Les tests doivent distinguer échappement texte et attribut.
