<?php

namespace App;

enum Drink: string
{
    case Whisky = 'whisky';
    case Vodka = 'vodka';
    case WhiteWine = 'white_wine';
    case RedWine = 'red_wine';
    case NonAlcoholic = 'non_alcoholic';

    /**
     * The name of the drink as guests see it.
     */
    public function label(): string
    {
        return match ($this) {
            self::Whisky => 'Виски',
            self::Vodka => 'Водка',
            self::WhiteWine => 'Вино белое',
            self::RedWine => 'Вино красное',
            self::NonAlcoholic => 'Не пью',
        };
    }

    /**
     * The bar code printed on the ticket stub.
     */
    public function code(): string
    {
        return match ($this) {
            self::Whisky => 'WSK',
            self::Vodka => 'VDK',
            self::WhiteWine => 'WHT',
            self::RedWine => 'RED',
            self::NonAlcoholic => 'N/A',
        };
    }

    /**
     * The drink as it reads inside a sentence: "На баре для вас — белое вино".
     */
    public function phrase(): string
    {
        return match ($this) {
            self::Whisky => 'виски',
            self::Vodka => 'водка',
            self::WhiteWine => 'белое вино',
            self::RedWine => 'красное вино',
            self::NonAlcoholic => 'безалкогольные напитки',
        };
    }

    /**
     * The glass icon drawn next to the drink.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Whisky => 'tumbler',
            self::Vodka => 'shot',
            self::WhiteWine => 'wine-white',
            self::RedWine => 'wine-red',
            self::NonAlcoholic => 'soft',
        };
    }
}
