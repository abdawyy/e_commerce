<?php

namespace App\Http\Controllers;

use App\Models\shoppingCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\productItems;
use App\Traits\Apptraits;
use App\Models\products;
use Illuminate\Support\Facades\Session;



class CartController extends Controller
{

    use Apptraits;

    public $model = 'App\Models\shoppingCart';
    public function add(Request $request)
    {
        $this->validateCartRequest($request);

        try {
            $productItem = $this->getProductItem($request);
            $availableStock = $productItem->quantity;
            $requestedQuantity = $request->quantity;

            if (auth()->check()) {
                session()->forget('cart');

                return $this->handleAuthCart($request, $productItem, $availableStock, $requestedQuantity);
            } else {
                return $this->handleGuestCart($request, $productItem, $availableStock, $requestedQuantity);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the product to the cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function validateCartRequest(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'size_id' => 'required|integer|exists:product_items,id',
            'quantity' => 'required|integer|min:1',
        ]);
    }

    private function getProductItem(Request $request)
    {
        $productItem = productItems::with('products')->where('id', $request->size_id)
            ->where('products_id', $request->product_id)
            ->first();

        if (!$productItem) {
            abort(response()->json([
                'success' => false,
                'message' => 'Product size not found.'
            ], 404));
        }

        return $productItem;
    }

    private function handleAuthCart($request, $productItem, $availableStock, $requestedQuantity)
    {
        $userId = auth()->id();

        $existingCartItem = ShoppingCart::where('user_id', $userId)
            ->where('products_id', $request->product_id)
            ->where('size_id', $request->size_id)
            ->first();

        $existingQuantity = $existingCartItem ? $existingCartItem->quantity : 0;
        $newTotalQuantity = $existingQuantity + $requestedQuantity;

        if ($newTotalQuantity > $availableStock) {
            return $this->stockLimitResponse($productItem, $availableStock);
        }

        if ($existingCartItem) {
            $existingCartItem->quantity = $newTotalQuantity;
            $existingCartItem->save();
            $message = 'Product quantity updated successfully.';
        } else {
            ShoppingCart::create([
                'user_id' => $userId,
                'products_id' => $request->product_id,
                'size_id' => $request->size_id,
                'quantity' => $requestedQuantity,
            ]);
            $message = 'Product added to cart successfully.';
        }

        $cartCount = ShoppingCart::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'cartCount' => $cartCount
        ]);
    }

    private function handleGuestCart($request, $productItem, $availableStock, $requestedQuantity)
    {
        $cart = session()->get('cart', []);
        $baseKey = $request->product_id . '_' . $request->size_id;

        // If the key exists already, use it. If not, generate a unique one
        $existingKey = collect($cart)->search(function ($item) use ($request) {
            return $item['product_id'] == $request->product_id && $item['size_id'] == $request->size_id;
        });

        $key = is_string($existingKey) ? $existingKey : $baseKey;

        $existingQuantity = isset($cart[$key]) ? $cart[$key]['quantity'] : 0;
        $newTotalQuantity = $existingQuantity + $requestedQuantity;

        if ($newTotalQuantity > $availableStock) {
            return $this->stockLimitResponse($productItem, $availableStock);
        }

        $cart[$key] = [
            'product_id' => $request->product_id,
            'size_id' => $request->size_id,
            'quantity' => $newTotalQuantity,
            'name' => optional($productItem->products)->name,
            'price' => $productItem->products->price,
            'sale' => $productItem->products->sale,
            'key' => $key // store key
        ];

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully.',
            'cartCount' => count($cart)
        ]);
    }


    private function stockLimitResponse($productItem, $availableStock)
    {
        $productName = optional($productItem->product)->name ?? 'Product';

        return response()->json([
            'success' => false,
            'message' => "Cannot add more than available stock for: {$productName}. Only {$availableStock} left.",
        ]);
    }


    public function index()
    {
        if (auth()->check()) {
            return $this->renderAuthCart();
        }

        return $this->renderGuestCart();
    }

    private function renderAuthCart()
    {
        $cartItems = ShoppingCart::with('product', 'productItems')
            ->where('user_id', auth()->id())
            ->get();

        $subtotal = $cartItems->sum(function ($item) {
            return ($item->product->price - ($item->product->price * $item->product->sale / 100)) * $item->quantity;
        });

        return view('cart.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);
    }

    private function renderGuestCart()
    {
        $sessionCart = session()->get('cart', []);
        $cartItems = collect();
        $subtotal = 0;

        // Group by product_id and size_id
        $combined = [];

        foreach ($sessionCart as $item) {
            $key = $item['product_id'] . '_' . $item['size_id'];

            if (!isset($combined[$key])) {
                $combined[$key] = $item;
            } else {
                // Combine quantity
                $combined[$key]['quantity'] += $item['quantity'];
            }
        }

        foreach ($combined as $item) {
            $product = \App\Models\products::find($item['product_id']);

            if ($product) {
                $cartItems->push((object) [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'sale' => $item['sale'],
                    'size_id' => $item['size_id'],
                    'key' => $key
                ]);

                $subtotal += ($product->price - ($product->price * $product->sale / 100)) * $item['quantity'];
            }
        }

        return view('cart.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);
    }



    public function delete($id)
    {
        // Call the deleteRecord function from the trait
        $isDeleted = self::deleteRecord($this->model, $id);

        // Handle the flash message based on the result
        if ($isDeleted) {
            // Success flash message
            return redirect()->back()->with('success', 'your item deleted successfully.');
        } else {
            // Failure flash message
            return redirect()->back()->with('error', 'Failed to delete your item');
        }
    }
    public function deleteGuest($key)
    {
        $guestCart = Session::get('cart', []);

        if (isset($guestCart[$key])) {
            unset($guestCart[$key]);
            Session::put('cart', $guestCart);

            return redirect()->back()->with('success', 'Item removed from cart.');
        }


        return redirect()->back()->with('error', 'Item not found in cart.');

    }
}
