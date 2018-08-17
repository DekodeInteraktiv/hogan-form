# Changelog

## 1.1.3
- Fixes the post reset problem [#19](https://github.com/DekodeInteraktiv/hogan-form/pull/19)

## 1.1.2
- Fixed missing call to `do_shortcode()` for Ninja Forms causing only the shortcode tag to rendered.

## 1.1.1
- Update module to new registration method introduced in [Hogan Core 1.1.7](https://github.com/DekodeInteraktiv/hogan-core/releases/tag/1.1.7)
- Set hogan-core dependency `"dekodeinteraktiv/hogan-core": ">=1.1.7"`

## 1.1.0
- Remove heading field, provided from Core in [#53](https://github.com/DekodeInteraktiv/hogan-core/pull/53)
- Breaking change: heading field has to be added using filter (was default on before).

## 1.0.6
- Changed default values for Gravity forms: form title and description not included by default.
