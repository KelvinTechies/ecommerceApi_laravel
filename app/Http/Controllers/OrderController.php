<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CartModel;
use App\Models\Order;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        if (auth('sanctum')->check()) {
            $user = auth('sanctum')->user()->id;
            if ($user) {
                $order = OrderItemModel::where('user_id', $user)->first();

                if ($order) {
                    $product = ProductModel::where('id', $order->product_id)->get();
                    return response([
                        "order" => $order,
                        "user" => $user,
                        "product" => $product,
                        "status" => 200
                    ]);
                } else {
                    return response([
                        "status" => 404,
                        "msg" => "No Order has been made yet."
                    ]);
                }
            }
        }
    }


    public function store(Request $request)
    {
        if (auth('sanctum')->check()) {
            $user = auth('sanctum')->user()->id;
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|max:191',
                'lastname' => 'required|max:191',
                'address' => 'required|max:191',
                'country' => 'required|max:191',
                'companyname' => 'max:191',
                'city' => 'required|max:191',
                'email' => 'required|max:191',
                'zip' => 'required|max:191'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => 422,
                    "errors" => $validator->messages()
                ]);
            } else {
                $order = new Order;
                $order->user_id = $user;
                $order->firstname = $request->firstname;
                $order->lastname = $request->lastname;
                $order->email = $request->email;
                $order->address = $request->address;
                $order->zip = $request->zip;
                $order->country = $request->country;
                $order->city = $request->city;
                $order->companyname = $request->companyname;
                // $order->payment_mode = $request->payment_mode;
                $order->tracking_no = "SaS_EcOm" . rand(1111, 9999);
                $order->save();


                $cart = CartModel::where('user_id', '=', $user)->get();
                $orderItems = [];
                foreach ($cart as $item) {
                    $orderItems[] = [
                        'product_id' => $item->product_id,
                        'qty' => $item->product_models->qty,
                        'price' => $item->product_models->selling_price,
                        'user_id' =>  $user

                    ];
                }

                $order->order_items()->createManyQuietly($orderItems);
                CartModel::destroy($cart);
                return response()->json([
                    "status" => 201,
                    "message" => "Order Placed Successfully",
                ]);
                /*  return response([
                    "order" => $order,
                ]);
                return response()->json([
                    "status" => 200,
                    "message" => "Order Placed Successfully"
                ]);
            }
            // $product_id = CartModel::where('user_id', '=', $user)->get();
            /*  foreach ($product_id as $item) {
                $order = Order::create([
                    'user_id' => $user,
                    'product_id' => $item->product_id,
                    'status' => 'pending'
                ]);
            } */


                // return response(["You are yet to order a Prroduct"]);
            }
        } else {
            return response([
                "status" => 401,
                "message" => "You are not logged in"
            ]);
        }
    }


    public function validateOrder(Request $request)
    {
        if (auth('sanctum')->check()) {
            $user = auth('sanctum')->user()->id;
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|max:191',
                'lastname' => 'required|max:191',
                'address' => 'required|max:191',
                'country' => 'required|max:191',
                'companyname' => 'max:191',
                'city' => 'required|max:191',
                'email' => 'required|max:191',
                'zip' => 'required|max:191'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => 422,
                    "errors" => $validator->messages()
                ]);
            } else {
                $order = new Order;
                $order->user_id = $user;
                $order->firstname = $request->firstname;
                $order->lastname = $request->lastname;
                $order->email = $request->email;
                $order->address = $request->address;
                $order->zip = $request->zip;
                $order->country = $request->country;
                $order->companyname = $request->companyname;
                $order->payment_mode = $request->payment_mode;
                $order->tracking_no = "SaS_EcOm" . rand(1111, 9999);
                $order->save();


                $cart = CartModel::where('user_id', '=', $user)->get();
                $orderItems = [];
                foreach ($cart as $item) {
                    $orderItems[] = [
                        'product_id' => $item->product_models->product_id,
                        'qty' => $item->product_qty,
                        'price' => $item->product_models->price
                    ];
                }

                $order->orderitems()->createMany($orderItems);
                CartModel::destroy($cart);
                return response()->json([
                    "status" => 200,
                    "message" => "Order Placed Successfully"
                ]);
            }
        } else {
            return response([
                "status" => 401,
                "message" => "You are not logged in"
            ]);
        }
    }
}
