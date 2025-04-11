<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

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
            'product_code' => $request->product_code,
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
}
