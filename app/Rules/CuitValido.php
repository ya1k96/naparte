<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CuitValido implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $digits = [];
        if (strlen($value) != 11) return false;
        for ($i = 0; $i < strlen($value); $i++) {
            if (!ctype_digit($value[$i])) return false;
            if ($i < 11) {
                $digits[] = $value[$i];
            }   
        }
        $acum = 0;
        foreach ([5, 4, 3, 2, 7, 6, 5, 4, 3, 2] as $i => $multiplicador) {
            $acum += $digits[$i] * $multiplicador;
        }
        $cmp = 11 - ($acum % 11);
        if ($cmp == 11) $cmp = 0;
        if ($cmp == 10) $cmp = 9;
        return($value[10] == $cmp);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El CUIT no es válido.';
    }
}
