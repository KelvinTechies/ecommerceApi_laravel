<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CategoryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    public function index()
    {
        $category = CategoryModel::where('status', '0')->get();
        if ($category) {
            return response()->json([
                'status' => 200,
                "category" => $category
            ]);
        }
    }
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required',
            'description' => 'required',
            'img' => 'required|image|mimes:jpeg,png,jpg|max:7048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 400,
                "errors" => $validator->messages()
            ]);
        }
        $category = new CategoryModel;
        $category->name = $request->input('name');
        $category->slug = $request->input('slug');
        $category->description = $request->input('description');
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . "." . $extension;
            $file->move('uploads/category/', $filename);
            $category->image = 'uploads/category/' . $filename;
        }
        $category->status = $request->input('status') == true ? "1" : "0";
        $category->save();

        return response()->json([
            "status" => 200,
            "message" => "Category Added"
        ]);
    }



    public function viewCategory()
    {
        $category = CategoryModel::all();

        return response()->json([
            'status' => 200,
            'category' => $category
        ]);
    }

    public function getSingleCategory($id)
    {
        $category = CategoryModel::find($id);

        if ($category) {
            return response()->json([
                'status' => 200,
                'category' => $category
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'Message' => "No Category Id Found"
            ]);
        }
    }

    public function updateCategory(Request $request, $id)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 422,
                "message" => $validator->messages()
            ]);
        } else {

            $category =  CategoryModel::find($id);
            if ($category) {

                $category->name = $request->input('name');
                $category->slug = $request->input('slug');
                $category->description = $request->input('description');
                $category->status = $request->input('status') == true ? "1" : "0";
                $category->save();

                return response()->json([
                    "status" => 200,
                    "message" => "Category Updated"
                ]);
            } else {

                return response()->json([
                    "status" => 404,
                    "message" => "Category Id not found"
                ]);
            }
        }
    }

    public function deleteCategory($id)
    {
        $category = CategoryModel::find($id);
        if ($category) {
            $category->delete();
            return response()->json([
                'status' => 200,
                'message' => "Deleted from Category List",
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "Category Id not Found",
            ]);
        }
    }
}
