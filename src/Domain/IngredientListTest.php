<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

use PHPUnit\Framework\TestCase;

final class IngredientListTest extends TestCase
{
    /** @test */
    public function it_can_be_an_empty_list(): void
    {
        self::assertCount(0, new IngredientList());
    }

    /** @test */
    public function it_returns_a_new_instance_when_adding_a_new_ingredient(): void
    {
        $ingredient = Cheese::fromAlias('mozzarella');
        $list = new IngredientList();
        $newList = $list->withIngredient($ingredient);

        self::assertCount(1, $newList);
        self::assertNotEquals($list, $newList);
        self::assertContains($ingredient, $newList);
    }

    /** @test */
    public function it_returns_a_new_instance_when_removing_an_ingredient(): void
    {
        $ingredient = Cheese::fromAlias('mozzarella');
        $list = new IngredientList($ingredient);
        $newList = $list->withoutIngredient($ingredient);

        self::assertCount(0, $newList);
        self::assertNotEquals($list, $newList);
        self::assertNotContains($ingredient, $newList);
    }

    /** @test */
    public function it_returns_the_same_instance_when_removing_not_found_ingredient(): void
    {
        $ingredient = Cheese::fromAlias('mozzarella');
        $unknownIngredient = Cheese::fromAlias('chevre');
        $list = new IngredientList($ingredient);
        $newList = $list->withoutIngredient($unknownIngredient);

        self::assertCount(1, $newList);
        self::assertSame($list, $newList);
        self::assertContains($ingredient, $newList);
        self::assertNotContains($unknownIngredient, $list);
    }
}
