<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\discountCodes; // Make sure you have the correct namespace for your discountCodes model
use App\Models\orders;

class ValidPromoCode implements Rule
{
    public function passes($attribute, $value)
    {
        // Step 1: Retrieve authenticated user ID
        $userId = Auth::id();

        // Step 2: Check if the promo code exists, is active, and not expired
        $promoCode = discountCodes::where('code', $value)
                                  ->where('is_active', 1)              // Promo must be active
                                  ->where('expiry_date', '>', now())   // Promo must not be expired
                                  ->first();

        if (!$promoCode) {
            return false; // Invalid, inactive, or expired promo code
        }

        // Step 3: Check if the user has an order
        $hasOrder = orders::where('user_id', $userId)->exists();

        // Optional logic: If you want to disallow using a promo if user already has an order
        if ($hasOrder) {
            return false; // User already has an order — not eligible
        }

        return true; // All conditions passed
    }

    public function message()
    {
        return 'The provided promo code is either invalid, expired, inactive, or not valid for your order.';
    }
}
