<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Converter;

use Bakame\PizzaKing\Domain\Cheese;
use Bakame\PizzaKing\Domain\Euro;
use Bakame\PizzaKing\Domain\IngredientList;
use Bakame\PizzaKing\Domain\Meat;
use Bakame\PizzaKing\Domain\Pizza;
use Bakame\PizzaKing\Domain\Sauce;
use PHPUnit\Framework\TestCase;

final class IngredientConverterTest extends TestCase
{
    private IngredientConverter $transformer;

    public function setUp(): void
    {
        $this->transformer = new IngredientConverter();
    }

    /** @test */
    public function test_it_can_convert_a_price(): void
    {
        $expected = [
            'currency' => 'EUR',
            'amount' => '1000.00',
        ];
        $price = Euro::fromCents(1_000_00);

        self::assertSame($expected, $this->transformer->euroToArray($price));
    }

    /** @test */
    public function it_can_convert_an_ingredient(): void
    {
        $expected = [
            'name' => 'mozzarella',
            'alias' => 'mozzarella',
            'price' => [
                'currency' => 'EUR',
                'amount' => '1.00',
            ],
        ];
        $ingredient = Cheese::fromAlias('MozZaRelLA', Euro::fromCents(1_00));

        self::assertSame($expected, $this->transformer->ingredientToArray($ingredient));
    }

    /** @test */
    public function test_it_can_convert_a_pizza_without_meat(): void
    {
        $expected = [
            'name' => 'pizza',
            'alias' => 'pizza',
            'basePrice' => [
                'currency' => 'EUR',
                'amount' => '4.00',
            ],
            'ingredients' => [
                [
                    'name' => 'tomato',
                    'alias' => 'tomato',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => '1.00',
                    ],
                ],
                [
                    'name' => 'goat',
                    'alias' => 'goat',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => '2.00',
                    ],
                ],
            ],
            'price' => [
                'currency' => 'EUR',
                'amount' => '7.00',
            ],
        ];

        $pizza = Pizza::fromIngredients(IngredientList::fromList([
            Sauce::fromAlias('tomato', Euro::fromCents(1_00)),
            Cheese::fromAlias('goat', Euro::fromCents(2_00)),
        ]));

        self::assertSame($expected, $this->transformer->dishToArray($pizza));
    }

    /** @test */
    public function test_it_can_convert_a_pizza_with_meats(): void
    {
        $expected = [
            'name' => 'pizza',
            'alias' => 'pizza',
            'basePrice' => [
                'currency' => 'EUR',
                'amount' => '4.00',
            ],
            'ingredients' => [
                [
                    'name' => 'goat',
                    'alias' => 'goat',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => '1.00',
                    ],
                ],
                [
                    'name' => 'tomato',
                    'alias' => 'tomato',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => '2.00',
                    ],
                ],
                [
                    'name' => 'ham',
                    'alias' => 'jambon',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => '3.00',
                    ],
                ],
            ],
            'price' => [
                'currency' => 'EUR',
                'amount' => '10.00',
            ],
        ];

        $pizza = Pizza::fromIngredients(IngredientList::fromList([
            Cheese::fromAlias('goat', Euro::fromCents(1_00)),
            Sauce::fromAlias('tomato', Euro::fromCents(2_00)),
            Meat::fromAlias('jambon', Euro::fromCents(3_00)),
        ]));

        self::assertSame($expected, $this->transformer->dishToArray($pizza));
    }
}
