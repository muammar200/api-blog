<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateSlug implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $name = request()->input('name') ?? request()->input('title');
        $expectedSlug = Str::slug($name);

        if ($value !== $expectedSlug) {
            if(request()->input('name')){
                $fail("The $attribute must match the name.");
            } elseif(request()->input('title')){
                $fail("The $attribute must match the title.");
            }
        }
    }
}
