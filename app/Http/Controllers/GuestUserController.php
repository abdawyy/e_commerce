<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuestUser;
class GuestUserController extends Controller
{
            public $url ='/admin/guest';


    public function list(Request $request)
    {
        // Get the search parameter from the request
        $search = $request->input('search');

        // Define the mapping of headers to fields
        $headerMap = [
            'ID' => 'id',
            'Name' => 'username',
            'Email' => 'email',
            'Created At' => 'created_at',

        ];

        // Use the search scope defined in the AppTrait
        $data = GuestUser::search($search, $headerMap)->paginate(10)->appends(['search' => $search]); // 👈 This preserves the search query;

        // Define the headers for the table
        $headers = ['ID', 'Name', 'Email', 'Created At', 'Action'];

        // Prepare the rows by mapping through the data collection
        $rows = $data->map(function ($admin) {
            return [
                'ID' => $admin->id,
                'Name' => $admin->name,
                'Email' => $admin->email,
                'Created At' => $admin->created_at->format('m/d/Y'),
            ];
        });
        $url = $this->url;

        // Return the view with headers and rows data
        return view('admin.guest.list', compact('headers', 'rows', 'data', 'search', 'url'));
    }
    public function guestShow($id)
    {
        // Retrieve the order with its related data, including product and size for orderItems
        $guest = GuestUser::with([
            'address',  // Load all addresses associated with the guest
            'orders.discountCodes',  // Load the discount code related to the order
            'orders.cities',
            'orders.address'           // Load the city related to the order
        ])->find($id);

        // Check if the order exists
        if (!$guest) {
            // Redirect back with an error message if the order is not found
            return redirect()->back()->with('error', 'guest not found');
        }

        // Pass the order data to the view
        return view('admin.guest.show', compact('guest'));
    }
}
