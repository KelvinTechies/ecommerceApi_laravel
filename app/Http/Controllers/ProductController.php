<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CategoryModel;
use App\Models\Order;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product =  ProductModel::all();
        if ($product) {
            return response()->json([
                'status' => 200,
                "product" => $product
            ]);
        }
    }

    public function allOrdersFOrAdmin()
    {
        $orders = Order::all();
        return response()->json([
            "orders" => $orders,
            "status" => 200
        ]);
    }
    public function orderForLoggedInUsers()
    {
        if (auth('sanctum')->check()) {
            $user = auth('sanctum')->user()->id;
            if ($user) {
                $order = OrderItemModel::where('user_id', $user)->first();

                if ($order) {
                    return response([
                        "order" => $order,
                        "user" => $user
                    ]);
                } else {
                    return response([
                        "status" => 400,
                        "msg" => "Not FOund"
                    ]);
                }
            }
        }
    }

    public function fetchSlug($slug)
    {
        $category = CategoryModel::where('slug', $slug)->where('status', '0')->first();

        if ($category) {
            $product = ProductModel::where('category_id', $category->id)->where('status', '0')->get();
            if ($product) {
                return response()->json([
                    'status' => 200,
                    "product_data" => [
                        'product' => $product,
                        "category" => $category

                    ],
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    "message" => "Not Found"
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                "message" => "Category Not Found"
            ]);
        }
    }

    public function viewCategory()
    {
        $category = CategoryModel::all();

        return response()->json([
            'status' => 200,
            'category' => $category
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'selling_price' => 'required',
            'original_price' => 'required',
            'qty' => 'required',
            'description' => 'required',
            'brand' => 'required',
            'slug' => 'required',
            // 'image' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                "errors" => $validator->messages()
            ]);
        } else {
            $product = new ProductModel;
            $product->name = $request->input('name');
            $product->slug = $request->input('slug');
            $product->category_id = $request->input('category_id');
            $product->description = $request->input('description');
            $product->qty = $request->input('qty');
            $product->selling_price = $request->input('selling_price');
            $product->original_price = $request->input('original_price');
            $product->brand = $request->input('brand');
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . "." . $extension;
                $file->move('uploads/product/', $filename);
                $product->image = 'uploads/product/' . $filename;
            }
            $product->featured = $request->input('featured') == true ? "1" : "0";
            $product->featured = $request->input('popular') == true ? "1" : "0";
            $product->featured = $request->input('status') == true ? "1" : "0";
            $product->save();
            return response()->json([
                'status' => 200,
                "message" => "Product Added"
            ]);
        }

        // return ProductModel::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)

    {

        $category = CategoryModel::where('id', $id)->where('status', 0)->first();

        if ($category) {
            $product = ProductModel::where('category_id', $category->id)->where('status', 0)->get();
            if ($product) {
                return response()->json([
                    'status' => 200,
                    "product_data" => [
                        'product' => $product
                    ],
                    "category" => $category
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    "message" => "Not Found"
                ]);
            }
        }
    }

    public function publicShow($id)

    {


        $product = ProductModel::where('id', $id)->where('status', 0)->get();
        if ($product) {
            return response()->json([
                'status' => 200,
                'products' => $product

            ]);
        } else {
            return response()->json([
                'status' => 404,
                "message" => "Not Found"
            ]);
        }
    }


    public function relatedProducts($id)

    {

        $pro = ProductModel::find($id);

        if ($pro) {
            $product = ProductModel::where('category_id', $pro->category_id)->where('status', 0)->get();
            if ($product) {
                return response()->json([
                    'status' => 200,
                    'products' => $product,
                    "id" => $id

                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    "message" => "Not Found"
                ]);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'selling_price' => 'required',
            'original_price' => 'required',
            'qty' => 'required',
            'description' => 'required',
            'brand' => 'required',
            'slug' => 'required',
            // 'image' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                "errors" => $validator->messages()
            ]);
        } else {
            $product = ProductModel::find($id);
            if ($product) {

                $product->name = $request->input('name');
                $product->slug = $request->input('slug');
                $product->category_id = $request->input('category_id');
                $product->description = $request->input('description');
                $product->qty = $request->input('qty');
                $product->selling_price = $request->input('selling_price');
                $product->original_price = $request->input('original_price');
                $product->brand = $request->input('brand');
                if ($request->hasFile('image')) {
                    $path = $product->image;
                    if (File::exists($path)) {
                        File::delete();
                    }
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . "." . $extension;
                    $file->move('uploads/product/', $filename);
                    $product->image = 'uploads/product/' . $filename;
                }
                $product->featured = $request->input('featured');
                $product->popular = $request->input('popular');
                $product->status = $request->input('status');
                $product->save();
                return response()->json([
                    'status' => 200,
                    // "message" => $product
                    "message" => "Product Updated"
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    "message" => "Product id not found"
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /*  $product = ProductModel::find($id);
        $product->delete();
        return $product; */

        return ProductModel::destroy($id);
    }


    /**
     * search the specified resource from storage.
     */
    public function search(string $name)
    {
        /*  $product = ProductModel::find($id);
        $product->delete();
        return $product; */

        return ProductModel::where('name', 'like', '%' . $name . '%')->get();
    }
}
