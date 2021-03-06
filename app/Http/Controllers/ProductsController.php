<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductForm;

class ProductsController extends Controller
{
    /**
     * Only user with access can have a profile.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * View that show the all products that the user want to sell.
     * @return view with array of products
     */
    public function index()
    {
        $products = auth()->user()->products()->get();

        return view('products.index', compact('products'));
    }

    /**
     * Create a new product.
     * @return view
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Edit the current product.
     * @param  Product $product
     * @return view products.edit
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * TODO : make the update
     * update the current product.
     * @param  Product $product
     * @return redirect
     */
    public function update(Product $product, ProductForm $request)
    {
        // update the data
        $product->update($request->validated());
        $product->save();

        // confirm the succes to the user
        session()->flash('message', ' Product updated with success !');

        // redirect to the profile user product
        return redirect('profile/products');
    }

    /**
     * destroy a product.
     * @param  Product $product
     * @return redirect to the products view
     */
    public function destroy(Product $product)
    {
        /*
        * delete the product
        */
        Product::destroy($product->id);

        /*
        * confirm the succes to the user
        */
        session()->flash('message', 'Delete product success !');

        return redirect('profile/products');
    }

    /**
     * Store the products in the database.
     * @param  Request $request
     * @return view
     */
    public function store(ProductForm $request)
    {
        $paths = [];

        // check if any files images has been sended
        if ($request->hasFile('image')) {
            // store the image in local and save the path
            $files = $request->allFiles('image')['image'];

            // store the files in the local drive
            foreach ($files as $file) {
                array_push($paths, $file->store('public/img/products'));
            }
        }

        // save the products
        auth()->user()->storeProduct(
            new Product(request(['name', 'price', 'description', 'isNegotiable'])),
            $paths
        );

        // confirm the succes to the user
        session()->flash('message', ' Product created with success !');

        // redirect to the profile user product
        return redirect('profile/products');
    }
}
