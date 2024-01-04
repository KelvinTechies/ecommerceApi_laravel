<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth('sanctum')->check();
        $user_id = auth('sanctum')->user()->id;
        if ($user) {
            $cart = CartModel::where('user_id', '=', $user_id)->get();

            return response([
                'cart' => $cart,
                'status' => 200
            ]);
        } else {
            return response([
                'message' => "You are not Logged in",
                'status' => 401
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function updateQuantity($cart_id, $scope)
    {
        if (auth('sanctum')->check()) {
            $user_id = auth('sanctum')->user()->id;
            $cart_item = CartModel::where('id', $cart_id)->where('user_id', $user_id)->first();

            if ($scope == 'inc') {
                $cart_item->product_qty += 1;
            } elseif ($scope == 'dec') {
                $cart_item->product_qty -= 1;
            }
            $cart_item->update();
            return response([
                'message' => "Quantity updated",
                'status' => 200
            ]);
        } else {
            return response([
                'message' => "You are not Logged in",
                'status' => 401
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (auth('sanctum')->check()) {
            $user_id = auth('sanctum')->user()->id;
            /*   $product_id=ProductModel::where()
            return response([
                "message" => $user
            ]); */


            $fields = $request->validate([
                'product_id' => 'required',
                'product_qty' => 'required',
            ]);

            $product_qty = $request->product_qty;
            $product_id = ProductModel::where('id', $fields['product_id'])->first();

            if (!$product_id) {
                return response("Product Not Found", 404);
            } else {
                // $pro_id = CartModel::where('product_id', $fields['product_id'])->first();
                $pro_id = CartModel::where('product_id', $fields['product_id'])->where('user_id', $user_id)->exists();
                if ($pro_id) {

                    return response()->json(["message" => $product_id->name . " Product Already in Cart ", "status" => 409]);
                } else {
                    $cart = CartModel::create([
                        'user_id' => $user_id,
                        'product_id' => $product_id->id,
                        "product_qty" => $product_qty
                    ]);

                    $response = [
                        'cart' => $cart,
                        "message" => $product_id->name . 'Added to Cart',
                        "status" => 201
                    ];

                    return response($response);
                }
            }
        } else {
            return response()->json([
                'status' => 401,
                "message" => "You are not Logged in"
            ]);
        };
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (auth('sanctum')->check()) {
            $user_id = auth('sanctum')->user()->id;

            $cart_item = CartModel::where('id', $id)->where('user_id', $user_id)->first();
            if ($cart_item) {
                $cart_item->delete();
                return response([
                    'message' => "Item Deleted From Cart",
                    "status" => 200
                ]);
            }
        } else {
            return response([
                'message' => "Login to Continue",
                "status" => 401
            ]);
        }
        // $cart = CartModel::destroy($id);


    }
}
