<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\shoppingCart; // Ensure you have the correct model
use Illuminate\Support\Facades\Auth;
use App\Traits\Apptraits;
use App\Models\addresses;
use App\Models\orderItems;
use App\Models\orders;
use App\Models\payments;
use App\Models\discountCodes;
use App\Rules\ValidPromoCode;
use App\Models\Cities;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminOrderNotificationMail;
use Illuminate\Support\Facades\Session;
use App\Models\GuestUser;




class CheckoutController extends Controller
{
    /**
     * Show the checkout address form with cart summary.
     *
     * @return \Illuminate\View\View
     */
    use Apptraits;

    public $addressModel = 'App\Models\addresses';
    public $orderModel = 'App\Models\orders';

    public $guestModel = 'App\Models\GuestUser';

    public function index()
    {
        if (auth()->check()) {
            return $this->renderAuthIndex();
        } else {
            return $this->renderGuestIndex();
        }

    }
   
    public function order(Request $request)
    {
        if (Auth::check()) {
            return $this->processAuthOrder($request);
        } else {
            return $this->processGuestOrder($request);
        }
    }
    protected function processAuthOrder(Request $request)
    {
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|digits_between:4,10',
            'phone_number' => 'required|numeric|digits_between:10,15',
            'promo_code' => ['nullable', 'string', 'max:50', new ValidPromoCode],
        ]);

        $promoCodeValue = discountCodes::where('code', $validatedData['promo_code'] ?? null)->first();
        $city = Cities::where('id', $validatedData['city'])->firstOrFail();
        $userId = Auth::id();

        // Update or create address

        $addressValues = $request->all();
        $addressValues['user_id'] = Auth::id();
        unset($addressValues['_token']); // Remove CSRF token
        self::updateOrCreate($this->addressModel, ['user_id' => $userId], $addressValues);

        // Calculate total
        $cart = new ShoppingCart();
        $total = $cart->totalPrice($userId, $city->price, $promoCodeValue->discount_percentage ?? 0);

        // Create order
        $order = self::updateOrCreate($this->orderModel, ['id' => ''], [
            'user_id' => $userId,
            'total_amount' => $total,
            'status' => 'pending',
            'discount_code_id' => $promoCodeValue->id ?? null,
            'city_id' => $city->id,
        ]);

        // Add items and handle failure
        if (!$order->addOrderItems($userId)['success']) {
            return redirect()->back()->with('error', 'Failed to add items to order.');
        }

        // Payment
        payments::createCashPayment($order->id, $total);

        // Load full order
        $order = $this->loadOrderWithRelations($order->id);

        // PDF & Email
        $pdfPath = self::generatePdfInvoice($order);
        Mail::to($order->user->email)->send(new OrderConfirmationMail($order, $pdfPath));
        Mail::to('hayah.mona@hotmail.com')->send(new AdminOrderNotificationMail($order, $pdfPath));

        return view('checkout.receipt', [
            'orderID' => $order->id,
            'totalPrice' => $total,
            'deleveryFees' => $city->price,
        ]);
    }
    protected function processGuestOrder(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
            'full_name' => 'required|string|max:255',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|digits_between:4,10',
            'phone_number' => 'required|numeric|digits_between:10,15',
            'promo_code' => ['nullable', 'string', 'max:50', new ValidPromoCode],
        ]);


        $promoCodeValue = discountCodes::where('code', $validatedData['promo_code'] ?? null)->first();
        $city = Cities::where('id', $validatedData['city'])->firstOrFail();
        $guestData = [
            'email' => $validatedData['email'],
            'name' => $validatedData['full_name'],

        ];
        $userId = null;

        // Optional: Save address anonymously
        $guestUser = self::updateOrCreate($this->guestModel, ['email' => $validatedData['email']], $guestData);


        $addressValues = $request->all();
        unset($addressValues['_token']); // Remove CSRF token


        $addressValues['user_id'] = null;
        $addressValues['guest_id'] = $guestUser->id;



        $address = self::updateOrCreate($this->addressModel, ['id' => ''], $addressValues);

        // Calculate total
        $cart = new ShoppingCart();
        $total = $cart->totalPrice(null, $city->price, $promoCodeValue->discount_percentage ?? 0);

        // Create order
        $order = self::updateOrCreate($this->orderModel, ['id' => ''], [
            'user_id' => null,
            'total_amount' => $total,
            'status' => 'pending',
            'discount_code_id' => $promoCodeValue->id ?? null,
            'city_id' => $city->id,
            'address_id' => $address->id,
            'guest_id'=>$guestUser->id
        ]);


        if (!$order->addOrderItems(null, true)['success']) {

            return redirect()->back()->with('error', 'Failed to add items to order.');
        }


        payments::createCashPayment($order->id, $total);

        $order = $this->loadOrderWithRelations($order->id);

        $pdfPath = self::generatePdfInvoice($order);
        Mail::to($guestUser->email)->send(new OrderConfirmationMail($order, $pdfPath));
        Mail::to('hayah.mona@hotmail.com')->send(new AdminOrderNotificationMail($order, $pdfPath));

        return view('checkout.receipt', [
            'orderID' => $order->id,
            'totalPrice' => $total,
            'deleveryFees' => $city->price,
        ]);
    }



    private function renderAuthIndex()
    {
        // Retrieve cart items for the authenticated user
        $cartItems = shoppingCart::with('product', 'productItems') // assuming a 'product' relation exists
            ->where('user_id', Auth::id())
            ->get();

        // Calculate total price
        $subtotal = $cartItems->sum(function ($item) {
            return ($item->product->price - ($item->product->price * $item->product->sale / 100)) * $item->quantity;
        });
        $total = $subtotal;

        // Return the checkout view with the necessary data
        return view('checkout.address', compact('cartItems', 'subtotal', 'total'));
    }
    private function renderGuestIndex()
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

        return view('checkout.address', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);


    }
    protected function loadOrderWithRelations($orderId)
    {
        return orders::with([
            'user',
            'user.address',            // Load user and their address
            'guestUser',               // In case the order belongs to a guest
            'guestUser.address',       // Guest user address
            'discountCodes',           // Discount codes
            'cities',                  // City of the order
            'orderItems.product',      // Product details
            'orderItems.productItems', // Product sizes/items
            'payments',
            'address'                 // Payment info
        ])->findOrFail($orderId);
    }
}
