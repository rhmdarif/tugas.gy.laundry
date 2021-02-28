<?php

namespace App\Rules\Voucher;

use Illuminate\Contracts\Validation\Rule;

class CheckCode implements Rule
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
        //
        $r = false;
        
        $query = \App\Voucher::where('id', $value);
        if($query->count()) {
            if($query->first()->status == 0) {
                $r = true;
            }
        }

        return $r;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
