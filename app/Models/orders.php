<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Apptraits;
use Illuminate\Support\Facades\DB;


class orders extends Model
{
    use Apptraits;
    use HasFactory;
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'discount_code_id',
        'city_id',
        'guest_id',
        'address_id'




    ];
    protected $table = 'orders';


    // Optionally, specify default values for attributes
    protected $attributes = [
        'status' => 'pending', // You can set a default status if needed
    ];

    public function user()
    {
        return $this->belongsTo(User::class);

    }
    public function discountCodes()
    {
        return $this->belongsTo(discountCodes::class, 'discount_code_id');

    }
    public function cities()
    {
        return $this->belongsTo(Cities::class, 'city_id');

    }
    public function orderDiscounts()
    {
        return $this->hasMany(orderDiscounts::class);

    }
    public function orderItems()
    {
        return $this->hasMany(orderItems::class, 'orders_id');

    }
    public function payments()
    {
        return $this->hasMany(payments::class, 'orders_id');

    }
    public function guestUser()
    {
        return $this->belongsTo(GuestUser::class , 'guest_id');
    }
    public function address(){
        return $this->belongsTo(addresses::class ,'address_id');
    }
    public function addOrderItems($userId, $isGuest = false)
    {
        DB::beginTransaction();

        try {
            // Get cart items based on user type
            $cartItems = $isGuest
                ? self::getGuestCartItems()
                : ShoppingCart::getCartItemsByUserId($userId);

            if (empty($cartItems) || (is_countable($cartItems) && count($cartItems) === 0)) {
                return ['success' => false, 'message' => 'Cart is empty.'];
            }

            // Step 1: Deduct stock and create order items
            $this->deductProductQuantitiesAndCreateOrderItems($cartItems);

            // Step 2: Clear cart after processing
            $this->clearCart($userId, $isGuest);

            DB::commit();
            return ['success' => true, 'message' => 'Order items added successfully.'];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Failed to add order items: ' . $e->getMessage()];
        }
    }

    protected function deductProductQuantitiesAndCreateOrderItems($cartItems)
    {
        foreach ($cartItems as $cartItem) {
            $productsId = $cartItem['product_id'] ?? $cartItem->products_id;
            $sizeId = $cartItem['size_id'] ?? $cartItem->size_id;
            $quantity = $cartItem['quantity'] ?? $cartItem->quantity;

            $productItem = productItems::where('products_id', $productsId)
                ->where('id', $sizeId)
                ->first();

            if (!$productItem) {
                throw new \Exception('Product item not found.');
            }

            $productItem->quantity = max(0, $productItem->quantity - $quantity);
            $productItem->save();
            $product = products::findOrFail($cartItem['product_id']);

            $price = ($product->price ?? 0) - (($product->price ?? 0) * ($product->sale ?? 0) / 100);
            $totalPrice = $price * $quantity;

            orderItems::create([
                'orders_id' => $this->id,
                'products_id' => $productsId,
                'quantity' => $quantity,
                'size' => $productItem->size,
                'price' => $totalPrice,
            ]);
        }
    }

    protected function clearCart($userId, $isGuest)
    {
        if ($isGuest) {
            session()->forget('cart');
        } else {
            ShoppingCart::where('user_id', $userId)->delete();
        }
    }



}
