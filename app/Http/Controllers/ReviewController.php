<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Http\Requests\ReviewRequest;
use App\Traits\Apptraits;


class ReviewController extends Controller
{
    use Apptraits;
    public $url = '/admin/reviews';


    public function store(ReviewRequest $request)
    {
        $request->validated();


        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Review submitted']);
    }
    public function list(Request $request, $productId)
    {
        // Get the search parameter from the request
        $search = $request->input('search');

        // Define the mapping of headers to fields
        $headerMap = [
            'ID' => 'id',
            'User Name' => 'user.name',
            'Rating' => 'rating',
            'Message' => 'message',
            'Created At' => 'created_at',
        ];

        // Get only reviews for the given product with eager loading for user
        $data = Review::with('user')
            ->where('product_id', $productId)
            ->search($search, $headerMap)
            ->paginate(10);

        // Define table headers
        $headers = ['ID', 'User Name', 'Rating', 'Comment', 'Created At','Action'];

        // Prepare rows
        $rows = $data->map(function ($data) {
            return [
                'ID' => $data->id,
                'User Name' => optional($data->user)->name,
                'Rating' => str_repeat('⭐', $data->rating),
                'Comment' => $data->comment,
                'Created At' => $data->created_at->format('m/d/Y'),
                'is_active'=>$data->is_active,
            ];
        });

        $url = $this->url;

        return view('admin.product.review', compact('headers', 'rows', 'data', 'search', 'url'));
    }

    public function toggleUserStatus($id)
    {
        $review = Review::findOrFail($id);

        self::toggleStatus($review);
        return back()->with('success', 'Status toggled');
    }
    

}
