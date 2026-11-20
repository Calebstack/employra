<?php
declare(strict_types=1);

namespace App\Support;

class NigerianStates
{
    public static function states(): array
    {
        return [
            'Abia',
            'Adamawa',
            'Akwa Ibom',
            'Anambra',
            'Bauchi',
            'Bayelsa',
            'Benue',
            'Borno',
            'Cross River',
            'Delta',
            'Ebonyi',
            'Edo',
            'Ekiti',
            'Enugu',
            'Gombe',
            'Imo',
            'Jigawa',
            'Kaduna',
            'Kano',
            'Katsina',
            'Kebbi',
            'Kogi',
            'Kwara',
            'Lagos',
            'Nasarawa',
            'Niger',
            'Ogun',
            'Ondo',
            'Osun',
            'Oyo',
            'Plateau',
            'Rivers',
            'Sokoto',
            'Taraba',
            'Yobe',
            'Zamfara',
        ];
    }

    public static function options(): array
    {
        $options = array_combine(self::states(), self::states());
        $options['Federal Capital Territory'] = 'Federal Capital Territory (FCT)';

        return $options;
    }

    public static function normalize(string $location): ?string
    {
        $location = trim($location);
        foreach (self::options() as $value => $label) {
            if (strcasecmp($value, $location) === 0 || strcasecmp($label, $location) === 0) {
                return $value;
            }
        }

        return match (strtolower($location)) {
            'abuja', 'abuja, nigeria', 'fct' => 'Federal Capital Territory',
            'ibadan', 'ibadan, nigeria' => 'Oyo',
            'mowe', 'mowe, ogun state', 'abeokuta', 'abeokuta, nigeria', 'ogun state, nigeria' => 'Ogun',
            'lagos, nigeria' => 'Lagos',
            default => null,
        };
    }
}
