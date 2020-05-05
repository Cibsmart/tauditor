<?php


namespace App\Casts;

use App\Address;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class AddressCast implements CastsAttributes
{

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return new Address(
            $attributes['address_line_1'],
            $attributes['address_line_2'],
            $attributes['address_city'],
            $attributes['address_state'],
            $attributes['address_country'],
        );
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return [
            'address_line_1'  => $value->address_line_1,
            'address_line_2'  => $value->address_line_2,
            'address_city'    => $value->address_city,
            'address_state'   => $value->address_state,
            'address_country' => $value->address_country,
        ];
    }
}
