<?php

namespace App\Enums;

/** @psalm-immutable */
enum Colour: string
{
    use Enum;
//    case PRIMARY = 'primary';
//    case SECONDARY = 'secondary';
//    case INFORMATION = 'info';
//    case SUCCESS = 'success';
//    case WARNING = 'warning';
//    case DANGER = 'danger';

    //Darker
    case RED_DARKER = 'red-darker';
    case ORANGE_DARKER = 'orange-darker';
    case YELLOW_DARKER = 'yellow-darker';
    case LIME_DARKER = 'lime-darker';
    case GREEN_DARKER = 'green-darker';
    case SKY_DARKER = 'sky-darker';
    case BLUE_DARKER = 'blue-darker';
    case VIOLET_DARKER = 'violet-darker';
    case GRAY_DARKER = 'gray-darker';

    //Dark
    case RED_DARK = 'red-dark';
    case ORANGE_DARK = 'orange-dark';
    case YELLOW_DARK = 'yellow-dark';
    case LIME_DARK = 'lime-dark';
    case GREEN_DARK = 'green-dark';
    case SKY_DARK = 'sky-dark';
    case BLUE_DARK = 'blue-dark';
    case VIOLET_DARK = 'violet-dark';
    case GRAY_DARK = 'gray-dark';

    //Default
    case RED = 'red';
    case ORANGE = 'orange';
    case YELLOW = 'yellow';
    case LIME = 'lime';
    case GREEN = 'green';
    case SKY = 'sky';
    case BLUE = 'blue';
    case VIOLET = 'violet';
    case GRAY = 'gray';

    //Light
    case RED_LIGHT = 'red-light';
    case ORANGE_LIGHT = 'orange-light';
    case YELLOW_LIGHT = 'yellow-light';
    case LIME_LIGHT = 'lime-light';
    case GREEN_LIGHT = 'green-light';
    case SKY_LIGHT = 'sky-light';
    case BLUE_LIGHT = 'blue-light';
    case VIOLET_LIGHT = 'violet-light';
    case GRAY_LIGHT = 'gray-light';

    //Lighter
    case RED_LIGHTER = 'red-lighter';
    case ORANGE_LIGHTER = 'orange-lighter';
    case YELLOW_LIGHTER = 'yellow-lighter';
    case LIME_LIGHTER = 'lime-lighter';
    case GREEN_LIGHTER = 'green-lighter';
    case SKY_LIGHTER = 'sky-lighter';
    case BLUE_LIGHTER = 'blue-lighter';
    case VIOLET_LIGHTER = 'violet-lighter';
    case GRAY_LIGHTER = 'gray-lighter';


    public function hexColour(): string
    {
        return static::getHexColours()[$this->value];
    }

    public static function getHexColours(): array
    {
        //todo needs a script to grab json config and map into enums
        return  [
            self::RED_DARKER->value => '#902529',
            self::ORANGE_DARKER->value => '#63371A',
            self::YELLOW_DARKER->value => '#63371A',
            self::LIME_DARKER->value => '#263A84',
            self::GREEN_DARKER->value => '#166534',
            self::SKY_DARKER->value => '#263A84',
            self::BLUE_DARKER->value => '#263A84',
            self::VIOLET_DARKER->value => '#4F4779',
            self::GRAY_DARKER->value => '#1F2937',

            self::RED_DARK->value => '#BF2F2D',
            self::ORANGE_DARK->value => '#884917',
            self::YELLOW_DARK->value => '#884917',
            self::LIME_DARK->value => '#2D4AB3',
            self::GREEN_DARK->value => '#15803D',
            self::SKY_DARK->value => '#2D4AB3',
            self::BLUE_DARK->value => '#2D4AB3',
            self::VIOLET_DARK->value => '#72569d',
            self::GRAY_DARK->value => '#374151',

            self::RED->value => '#F4604D',
            self::ORANGE->value => '#D68724',
            self::YELLOW->value => '#D68724',
            self::LIME->value => '#4683E7',
            self::GREEN->value => '#22C55E',
            self::SKY->value => '#4683E7',
            self::BLUE->value => '#4683E7',
            self::VIOLET->value => '#A68CD1',
            self::GRAY->value => '#6B7280',

            self::RED_LIGHT->value => '#F6BEB5',
            self::ORANGE_LIGHT->value => '#EED492',
            self::YELLOW_LIGHT->value => '#EED492',
            self::LIME_LIGHT->value => '#ABD1F2',
            self::GREEN_LIGHT->value => '#86EFAC',
            self::SKY_LIGHT->value => '#ABD1F2',
            self::BLUE_LIGHT->value => '#ABD1F2',
            self::VIOLET_LIGHT->value => '#C4B3E2',
            self::GRAY_LIGHT->value => '#D1D5DB',

            self::RED_LIGHTER->value => '#FCF2EE',
            self::ORANGE_LIGHTER->value => '#FAF6E9',
            self::YELLOW_LIGHTER->value => '#FAF6E9',
            self::LIME_LIGHTER->value => '#E9F5FA',
            self::GREEN_LIGHTER->value => '#DCFCE7',
            self::SKY_LIGHTER->value => '#E9F5FA',
            self::BLUE_LIGHTER->value => '#E9F5FA',
            self::VIOLET_LIGHTER->value => '#F5E9FF',
            self::GRAY_LIGHTER->value => '#F3F4F6',
        ];
    }
}
