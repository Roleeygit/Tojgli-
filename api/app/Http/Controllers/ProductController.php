<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Product as ProductResource;

class ProductController extends BaseController
{
    public function ProductList()
    {
        $products = Product::with("category")->get();

        return $this->sendResponse(ProductResource::collection($products), "Got Products list!");
    }

    public function NewProduct(Request $request)
    {
        $input = $request->all();

        DB::statement("ALTER TABLE products AUTO_INCREMENT = 1;");
        
        $input["category_id"] = Category::where("category", $input["category"])->value("id");

        $validator = Validator::make($input,
        [
            "name" => "required",
            "price" => "required",
            "weight" => "required",
            "description" => "required",
            "category" => "required"
        ],
        [
            "name.required" => "The product's name field is required!",
            "price.required" => "The price's name field is required!",
            "weight.required" => "The weight's name field is required!",
            "description.required" => "The product's description field is required!",
            "category.required" => "The product's category field is required!"
        ]);
 
        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $product = Product::create($input);

        return $this->sendResponse(new ProductResource($product), "Product created!");

    }

    public function AddImageToProduct(Request $request, $image)
    {
        $input = $request->all();

        $validator = Validator::make($input,
        [
            "image" => "required"
        ],
        [
            "image.required" => "It's mandatory to add an image!"
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $product = Product::find($image);
        $product->update($request->all());
        $product->save();


        return $this->sendResponse(new productResource($product), "The picture added to the product!");

    }

    public function ShowProductById ($id)
    {
        $product = Product::find($id);

        if(is_null($product))
        {
            return $this->sendError("There is no product with this id($id)!");
        }

        return $this->sendResponse(new ProductResource($product), "Product loading was successfull.");
        
    }

    public function UpdateProduct(Request $request, $id)
    {
        $input = $request->all();

        if(is_null($input))
        {
            return $this->sendError("There is no product with this id($id)!");
        }
        
        $validator = Validator::make($input,
        [
            "name" => "required",
            "price" => "required",
            "weight" => "required",
            "description" => "required",
        ],
        [
            "name.required" => "The product's name field is required!",
            "price.required" => "The price's name field is required!",
            "weight.required" => "The weight's name field is required!",
            "description.required" => "The product's description field is required!"
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $product = Product::find($id);
        $product->update($request->all());
        $product->save();

        return $this->sendResponse(new productResource($product), "The product has been updated!"); 
    }

    public function DeleteProduct($id)
    {
        Product::destroy($id);
        
        $product = Product::find($id);
        $products = Product::where("id", ">", $id)->orderBy("id")->get();
        foreach($products as $product)
        {
            $product->id = $product->id -1;
            $product->save();
        }
        return $this->sendResponse([], "Product has been deleted successfully!");
    }

}
