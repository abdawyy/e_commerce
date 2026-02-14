<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\products;
use App\Models\productItems;
use App\Models\Category;
use App\Models\Type;
use App\Models\productImages;

use App\Traits\Apptraits;

class ProductController extends Controller
{
    use Apptraits;
    public $model = 'App\Models\products';
    public $assocatation = 'App\Models\productItems';

    public $url = '/admin/products';
    public $assocatationImage = 'App\Models\productImages';




    public $images = 'App\Models\productImages';
    public function edit(Request $request, $id = null)
    {
        // Retrieve the model instance to update (if $id is provided

        $model = $this->model::with('category', 'productItems', 'productImages')->find($id);




        if ($request->isMethod('post')) {
            // Validate the incoming request

            self::filterQuantities($request);

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',  // Allow null for description if it's optional
                'price' => 'required|numeric|min:0', // Ensure price is a positive number
                'sale' => 'nullable|numeric|min:0|max:100', // Sale percentage between 0 and 100
                'quantities' => 'required_without_all:quantities.S,quantities.M,quantities.L,quantities.XL,quantities.XXL,quantities.XXXL|array',
                'category_id' => 'required|integer|exists:categories,id', // Ensure category exists
                'type_id' => 'required|integer|exists:type,id', // Ensure category exists

                'color' => 'required|string|max:255',
                'image' => 'nullable|array', // Allow an array of images
                'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Each image validation
            ]);

            // Prepare the attributes and values for update or creation
            $attributes = [
                'id' => $id
            ];

            $values = [
                'name' => $request->name,
                'price' => $request->price,
                'sale' => $request->sale,
                'category_id' => $request->category_id,
                'type_id' => $request->type_id,
                'color' => $request->color,
                'description' => $request->description
            ];
            $quantities = $request->quantities;
            $files = $request->images;
            $imagesModel = new ProductImages();





            // Use updateOrCreate to handle update or creation
            $model = self::updateOrCreate($this->model, $attributes, $values);

            // Redirect with success or error messages
            if ($model) {
                self::uploadImages($request, $imagesModel, $model->id);

                $this->saveProductItems($quantities, $model);
                return redirect()->route('products.list')->with('success', 'Your product has been successfully saved.');
            } else {
                return redirect()->route('products.edit')->with('error', 'Your product could not be saved.');
            }
        }
        $categories = Category::all();
        $types = Type::all();


        // Return the view with the model data for editing
        return view('admin.product.edit', compact('model', 'categories', 'types'));
    }
    public function saveProductItems($quantities, $model)
    {


        foreach ($quantities as $size => $quantity) {
            // $productItem = ProductItems::where('products_id', $model->id)
            // ->where('size', $size)
            // ->get();

            $values = [
                'quantity' => $quantity,
                'size' => $size,
                'products_id' => $model->id
            ];


            // Define the attributes to check for existence
            $attributes = [
                'products_id' => $model->id,
                'size' => $size
            ];

            self::updateOrCreate($this->assocatation, $attributes, $values);
        }
    }
    public function list(Request $request)
    {
        // Get the search and filter parameters from the request
        $search = $request->input('search');
        $category_id = $request->input('category_id');
        $type_id = $request->input('type_id');
        $color = $request->input('color');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $stock_status = $request->input('stock_status');

        // Define the mapping of headers to fields
        $headerMap = [
            'ID' => 'id',
            'Name' => 'name',
            'Price' => 'price',
            'Sale' => 'sale',
            'Type' => 'Type.name',
            'Description' => 'description',
            'Color' => 'color',
            'Category Name' => 'Category.name', // Assuming relation is named `category`
            'Created At' => 'created_at',

        ];

        // Build the query with filters
        $query = products::with('category', 'type', 'productItems');
        
        // Apply search
        if ($search) {
            $query = $query->search($search, $headerMap);
        }
        
        // Apply category filter
        if ($category_id) {
            $query = $query->where('category_id', $category_id);
        }
        
        // Apply type filter
        if ($type_id) {
            $query = $query->where('type_id', $type_id);
        }
        
        // Apply color filter
        if ($color) {
            $query = $query->where('color', $color);
        }
        
        // Apply price range filter
        if ($minPrice !== null && $minPrice !== '') {
            $query = $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $query = $query->where('price', '<=', $maxPrice);
        }
        
        // Apply stock status filter
        if ($stock_status) {
            if ($stock_status === 'in_stock') {
                $query = $query->whereHas('productItems', function ($q) {
                    $q->where('quantity', '>', 0);
                });
            } elseif ($stock_status === 'out_of_stock') {
                $query = $query->where(function ($q) {
                    $q->whereDoesntHave('productItems')
                      ->orWhereHas('productItems', function ($q2) {
                          $q2->where('quantity', '<=', 0);
                      });
                });
            }
        }

        // Paginate with appends for filter preservation
        $data = $query->paginate(10)->appends([
            'search' => $search,
            'category_id' => $category_id,
            'type_id' => $type_id,
            'color' => $color,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'stock_status' => $stock_status
        ]);

        // Define the headers for the table
        $headers = ['ID', 'Name', 'Created At', 'Price', 'Sale', 'Type', 'Description', 'Color', 'Category Name', 'Action'];

        // Prepare the rows by mapping through the types collection
        $rows = $data->map(function ($data) {
            return [
                'ID' => $data->id,
                'Name' => $data->name,
                'Price' => $data->price,
                'Sale' => $data->sale,
                'Type' => $data->type->name,
                'Description' => $data->description,
                'Color' => $data->color,
                'Category Name' => $data->category->name,
                'Created At' => $data->created_at->format('m/d/Y'),
                'is_active' => $data->is_active,
                'is_highest' => $data->is_highest

            ];
        });

        $url = $this->url;

        // Get filter options for the view
        $categories = Category::all();
        $types = Type::all();
        $colors = products::distinct()->pluck('color')->filter()->values();
        $maxProductPrice = products::max('price');
        
        // Return the view with headers and rows data
        return view('admin.product.list', compact('headers', 'rows', 'data', 'search', 'url', 'categories', 'types', 'colors', 'maxProductPrice', 'category_id', 'type_id', 'color', 'minPrice', 'maxPrice', 'stock_status'));
    }
    public function delete($id)
    {
        // Call the deleteRecord function from the trait
        $isDeleted = self::deleteRecord($this->model, $id, true);

        // Handle the flash message based on the result
        if ($isDeleted) {
            // Success flash message
            return redirect()->back()->with('success', 'Record deleted successfully.');
        } else {
            // Failure flash message
            return redirect()->back()->with('error', 'Failed to delete the record. Record may not exist.');
        }
    }
    public function productWebList(Request $request, $id = null)
    {
        // Get filter parameters from the request
        $search = $request->input('search');
        $type_id = $request->input('type_id');
        $color = $request->input('color');
        $size = $request->input('size');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $stock_status = $request->input('stock_status');

        // Base query for products
        $query = products::with([
            'category',
            'type',
            'orderItems',
            'shoppingCart',
            'productItems',
            'productImages'
        ])
            ->where('is_active', 1)
            ->whereHas('category', function ($q) {
                $q->where('is_active', 1);
            })
            ->whereHas('type', function ($q) {
                $q->where('is_active', 1);
            });

        // Apply category filter
        if ($id) {
            $query->where('category_id', $id);
        }

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply type filter
        if ($type_id) {
            $query->where('type_id', $type_id);
        }

        // Apply color filter
        if ($color) {
            $query->where('color', $color);
        }

        // Apply price range filter
        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price', '<=', $maxPrice);
        }

        // Apply size filter
        if ($size) {
            $query->whereHas('productItems', function ($q) use ($size) {
                $q->where('size', $size);
            });
        }

        // Apply stock status filter
        if ($stock_status) {
            if ($stock_status === 'in_stock') {
                $query->whereHas('productItems', function ($q) {
                    $q->where('quantity', '>', 0);
                });
            } elseif ($stock_status === 'out_of_stock') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('productItems')
                      ->orWhereHas('productItems', function ($q2) {
                          $q2->where('quantity', '<=', 0);
                      });
                });
            }
        }

        // Build query string for pagination links
        $queryParams = [
            'search' => $search,
            'type_id' => $type_id,
            'color' => $color,
            'size' => $size,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'stock_status' => $stock_status
        ];

        // Sort and paginate
        $data = $query->orderByDesc('is_highest')
            ->orderByDesc('created_at')
            ->paginate(30)
            ->appends($queryParams);

        // Fetch all categories and filter options for the view
        $categories = Category::all();
        $types = Type::all();
        $colors = products::distinct()->pluck('color')->filter()->values();
        $sizes = productItems::distinct()->pluck('size')->filter()->values();
        $maxProductPrice = products::max('price');
        $categoryName = Category::find($id);


        return view('product.list', compact('data', 'categories', 'types', 'colors', 'sizes', 'maxProductPrice', 'id', 'categoryName', 'search', 'type_id', 'color', 'size', 'minPrice', 'maxPrice', 'stock_status'));
    }

    public function productWebShow($id)
    {
        // Fetch the product along with its related models using eager loading
        $product = products::with([
            'category',         // Fetch the associated category
            'type',             // Fetch the associated type
            'orderItems',       // Fetch the associated order items
            'shoppingCart',     // Fetch shopping cart items
            'productItems',     // Fetch product items
            'productImages'     // Fetch product images
        ])->find($id);

        // Check if the product exists
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        // Return the product data to a view
        return view('product.show', compact('product'));
    }
    public function deleteImage($id)
    {
        // Call the deleteRecord function from the trait
        $isDeleted = self::deletePerImage($this->assocatationImage, $id);

        // Handle the flash message based on the result
        if ($isDeleted) {
            // Success flash message
            return redirect()->back()->with('success', 'Image deleted successfully.');
        } else {
            // Failure flash message
            return redirect()->back()->with('error', 'Failed to delete the image. Image may not exist.');
        }

    }
    public function toggleUserStatus($id)
    {
        $product = products::findOrFail($id);

        self::toggleStatus($product);
        return back()->with('success', 'Status toggled');
    }

    public function toggleHighestStatus($id)
    {
        $product = products::findOrFail($id);

        self::toggleStatus($product, "is_highest");
        return back()->with('success', 'Status toggled');
    }



}
