Pizza King
----

[![Build Status](https://github.com/bakame-php/pizza-king/workflows/build/badge.svg)](https://github.com/bakame-php/pizza-king/actions?query=workflow%3A%22build%22)

This is a possible implementation to [mnapoli/ pizza-king](https://github.com/mnapoli/pizza-king) exercise.

The exercise
-------

This is a raw translation of the exercise in english:

> My name is Jean-Michel and I make pizzas in my pizzeria.
>
> In my pizzeria, customers can compose pizzas.
>
> And I need to digitize the composition of pizzas (necessarily).
>
> The application will allow me to make sure that only valid pizzas are created, 
>
> and to calculate the price of a pizza.
>
> Here are the rules for composing pizzas:
>
> - a pizza must have a sauce: tomato or cream.
> - a pizza must have a cheese: mozzarella or goat cheese.
> - a pizza has 0, 1 or 2 meats: ham and/or pepperoni and/or nothing.
>
> Here is the price of the ingredients:
>
> - basic price of a pizza : 4 €.
> - tomato sauce : 1 €
> - cream sauce : 1 €.
> - mozzarella : 3 €.
> - goat : 2 €.
> - ham : 2 €.
> - pepperoni : 4 €.
>
> It turns out that the pizzeria's customers are PHP developers.
> They will place an order via a PHP script, calling the necessary classes and/or functions.
> Pizzeria 2.0!
>
> Constraints :
>
> - you are not allowed to use a server database, like MySQL (because my cousin told me it's not web-scale)
> - no need to do a web UI or CLI
>
> Objective :
>
> - use the maximum of PHP 8 unique features (my cousin told me it was better)

System Requirements
-------

You need:

- **PHP8+** but the latest stable version of PHP is recommended

Reference
-------

The following PHP features are used:

#### PHP 8

- [match expression](https://wiki.php.net/rfc/match_expression_v2)
- [constructor promotion](https://wiki.php.net/rfc/constructor_promotion)

#### PHP 7.4

- [type properties](https://wiki.php.net/rfc/typed_properties_v2)
- [numeric literal separator](https://wiki.php.net/rfc/numeric_literal_separator)
- [arrow function](https://wiki.php.net/rfc/arrow_functions_v2)

Example
-------

A simple example can be found by running

```
make order
```

Testing
-------

`bakame/pizza-king` has:

- a [PHPUnit](https://phpunit.de) test suite
- a code analysis compliance test suite using [PHPStan](https://phpstan.org).
- a code analysis compliance test suite using [Psalm](https://psalm.dev).
- a coding style compliance test suite using [PHP CS Fixer](https://cs.symfony.com).

To run the tests, run the following command from the project folder.

``` bash
$ composer test
```
