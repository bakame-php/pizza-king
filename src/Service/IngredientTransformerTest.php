<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Service;

use Bakame\PizzaKing\Model\Cheese;
use Bakame\PizzaKing\Model\Euro;
use Bakame\PizzaKing\Model\Meat;
use Bakame\PizzaKing\Model\Pizza;
use Bakame\PizzaKing\Model\Sauce;
use PHPUnit\Framework\TestCase;

final class IngredientTransformerTest extends TestCase
{
    private IngredientTransformer $transformer;

    public function setUp(): void
    {
        $this->transformer = new IngredientTransformer();
    }

    /** @test */
    public function test_it_can_convert_a_price(): void
    {
        $expected = [
            'currency' => 'EUR',
            'amount' => '1000.00',
        ];
        $price = Euro::fromCents(1_000_00);

        self::assertSame($expected, $this->transformer->priceToArray($price));
    }

    /** @test */
    public function it_can_convert_an_ingredient(): void
    {
        $expected = [
            'type' => 'cheese',
            'name' => 'mozzarella',
            'price' => [
                'currency' => 'EUR',
                'amount' => '1.00',
            ],
        ];
        $ingredient = Cheese::fromName('MozZaRelLA', Euro::fromCents(1_00));

        self::assertSame($expected, $this->transformer->ingredientToArray($ingredient));
    }

    /** @test */
    public function test_it_can_convert_a_pizza_without_meat(): void
    {
        $expected = [
            'type' => 'pizza',
            'name' => 'pizza',
            'price' => [
                'currency' => 'EUR',
                'amount' => '7.00',
            ],
            'ingredients' => [
                [
                    'type' => 'cheese',
                    'name' => 'goat',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => '2.00',
                    ],
                ],
                [
                    'type' => 'sauce',
                    'name' => 'tomato',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => '1.00',
                    ],
                ],
            ],
        ];

        $pizza = Pizza::fromIngredients([
            Sauce::fromName('tomato', Euro::fromCents(1_00)),
            Cheese::fromName('goat', Euro::fromCents(2_00)),
        ]);

        self::assertSame($expected, $this->transformer->pizzaToArray($pizza));
    }

    /** @test */
    public function test_it_can_convert_a_pizza_with_meats(): void
    {
        $expected = [
            'type' => 'pizza',
            'name' => 'pizza',
            'price' => [
                'currency' => 'EUR',
                'amount' => '10.00',
            ],
            'ingredients' => [
                [
                    'type' => 'cheese',
                    'name' => 'goat',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => '1.00',
                    ],
                ],
                [
                    'type' => 'sauce',
                    'name' => 'tomato',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => '2.00',
                    ],
                ],
                [
                    'type' => 'meat',
                    'name' => 'jambon',
                    'price' => [
                        'currency' => 'EUR',
                        'amount' => '3.00',
                    ],
                ],
            ],
        ];

        $pizza = Pizza::fromIngredients([
            Cheese::fromName('goat', Euro::fromCents(1_00)),
            Sauce::fromName('tomato', Euro::fromCents(2_00)),
            Meat::fromName('jambon', Euro::fromCents(3_00)),
        ]);

        self::assertSame($expected, $this->transformer->pizzaToArray($pizza));
    }
}