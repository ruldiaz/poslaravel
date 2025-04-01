<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Intervention\Image\Facades\Image;


use Carbon\Carbon;

class SupplierController extends Controller
{
    public function AllSupplier() {
        $supplier = Supplier::latest()->get();
        return view('backend.supplier.all_supplier', compact('supplier'));
    }// end method

    public function AddSupplier() {
        return view('backend.supplier.add_supplier');
    } // end method

    public function StoreSupplier(Request $request) {
        $validateData = $request->validate([
            'name' => 'required|max:200',
            'email' => 'required|unique:customers|max:200',
            'phone' => 'required|max:200',
            'address' => 'required|max:400',
            'shopname' => 'required|max:200',
            'account_holder' => 'required|max:200',
            'account_number' => 'required',
            'type' => 'required',
            'image' => 'required',
        ]);

        if ($request->file('image')) {
            $image = $request->file('image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('upload/supplier/');

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
            $save_url = 'upload/supplier/' . $name_gen;
        } else {
            $save_url = null; // O establece una imagen por defecto si es obligatorio
        }

        Supplier::insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'shopname' => $request->shopname,
            'type' => $request->type,
            'account_holder' => $request->account_holder,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'bank_branch' => $request->bank_branch,
            'city' => $request->city,
            'image' => $save_url,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Supplier Inserted Succesfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.supplier')->with($notification);
    }// end method
}
