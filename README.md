# Forms Module for [Hogan](https://github.com/dekodeinteraktiv/hogan-core)

## Installation
Install the module using Composer `composer require dekodeinteraktiv/hogan-form` or simply by downloading this repository and placing it in `wp-content/plugins`

## Usage
Currently supports Gravity Forms and Contact Form 7

## Available filters
- `hogan/module/form/gravity_forms/options` for passing args to the Gravity Forms' render function.
```
//default values
$gs_defaults = [
    'display_title'       => true,
    'display_description' => true,
    'display_inactive'    => false,
    'field_values'        => null,
    'ajax'                => false,
    'tabindex'            => 1,
];

```

Options returned from the filter will be merged with defaults using `wp_parse_args()`
```
add_filter('hogan/module/form/gravity_forms/options', function() {
	return ['display_title' => false];
});
```
