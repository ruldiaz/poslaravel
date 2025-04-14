<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class ProductController extends Controller
{
    public function AllProduct() {
        $product = Product::latest()->get();
        return view('backend.product.all_product', compact('product'));
    } // end method

    public function AddProduct() {
        $category = Category::latest()->get();
        $supplier = Supplier::latest()->get();
        return view('backend.product.add_product', compact('category','supplier'));
    } // end method

    public function StoreProduct(Request $request) {
        
        $pcode = IdGenerator::generate(['table' => 'products', 'field' => 'product_code', 'length' => 4, 'prefix' => 'PC']);

        if ($request->file('product_image')) {
            $image = $request->file('product_image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('upload/product/');

            // Obtener la extensión de la imagen
            $imgExtension = $image->getClientOriginalExtension();
            
            // Crear imagen a partir del archivo
            if ($imgExtension == 'jpg' || $imgExtension == 'jpeg') {
                $img = imagecreatefromjpeg($image->getRealPath());
            } elseif ($imgExtension == 'png') {
                $img = imagecreatefrompng($image->getRealPath());
            } elseif ($imgExtension == 'gif') {
                $img = imagecreatefromgif($image->getRealPath());
            } else {
                // Si no es un tipo de imagen soportado
                $notification = array(
                    'message' => 'Invalid Image Format',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }

            // Redimensionar la imagen a 300x300
            $imgResized = imagescale($img, 300, 300);

            // Guardar la imagen redimensionada en el directorio
            if ($imgExtension == 'jpg' || $imgExtension == 'jpeg') {
                imagejpeg($imgResized, $destinationPath . $name_gen);
            } elseif ($imgExtension == 'png') {
                imagepng($imgResized, $destinationPath . $name_gen);
            } elseif ($imgExtension == 'gif') {
                imagegif($imgResized, $destinationPath . $name_gen);
            }

            // Liberar memoria
            imagedestroy($img);
            imagedestroy($imgResized);

            // Guardar la URL de la imagen
            $save_url = 'upload/product/' . $name_gen;
        } else {
            $save_url = null; // O establece una imagen por defecto si es obligatorio
        }

        Product::insert([
            'product_name' => $request->product_name,
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'product_code' => $pcode,
            'product_garage' => $request->product_garage,
            'product_store' => $request->product_store,
            'buying_date' => $request->buying_date,
            'expire_date' => $request->expire_date,
            'buying_price' => $request->buying_price,
            'selling_price' => $request->selling_price,
            'product_image' => $save_url,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Product Inserted Succesfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.product')->with($notification);
    }// end method

    public function EditProduct($id) {
        $product = Product::findOrFail($id);
        $category = Category::latest()->get();
        $supplier = Supplier::latest()->get();
        return view('backend.product.edit_product', compact('product','category','supplier'));
    } // end method

    public function UpdateProduct(Request $request)
    {
        $product_id = $request->id;
    
        $request->validate([
            'product_name' => 'required|max:255',
            'category_id' => 'required|integer',
            'supplier_id' => 'required|integer',
            'product_code' => 'required|max:100',
            'product_garage' => 'nullable|max:100',
            'product_store' => 'nullable|max:100',
            'buying_date' => 'nullable|date',
            'expire_date' => 'nullable|date',
            'buying_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
        ]);
    
        $product = Product::findOrFail($product_id);
    
        $save_url = $product->product_image; // usa la imagen actual por defecto
    
        if ($request->file('product_image')) {
            if ($product->product_image && file_exists(public_path($product->product_image))) {
                unlink(public_path($product->product_image));
            }
    
            $image = $request->file('product_image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('upload/product/');
    
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
    
            $image->move($destinationPath, $name_gen);
            $save_url = 'upload/product/' . $name_gen;
        }
    
        $updateData = [
            'product_name' => $request->product_name,
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'product_code' => $request->product_code,
            'product_garage' => $request->product_garage,
            'product_store' => $request->product_store,
            'buying_date' => $request->buying_date,
            'expire_date' => $request->expire_date,
            'buying_price' => $request->buying_price,
            'selling_price' => $request->selling_price,
            'product_image' => $save_url,
        ];
    
        $product->update($updateData);
    
        $notification = [
            'message' => 'Product Updated Successfully',
            'alert-type' => 'success',
        ];
    
        return redirect()->route('all.product')->with($notification);
    } // end method

    public function DeleteProduct($id) {
        $product_img = Product::findOrFail($id);
        $img = $product_img->product_image;
        unlink($img);

        Product::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Product Deleted Successfully',
            'alert-type' => 'success'
        );
    
        return redirect()->back()->with($notification);
    } // end method

    public function BarcodeProduct($id) {
        $product = Product::findOrFail($id);
        return view('backend.product.barcode_product', compact('product'));
    } // end method

    public function ImportProduct() {
        return view('backend.product.import_product');
    } // end method

    
}
