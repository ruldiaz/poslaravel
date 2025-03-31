<?php

namespace App\Http\Controllers\Backend;




use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Intervention\Image\Facades\Image;


use Carbon\Carbon;

class EmployeeController extends Controller
{
    //
    public function AllEmployee() {
        $employee = Employee::latest()->get();
        return view('backend.employee.all_employee', compact('employee'));
    }// end method

    public function AddEmployee() {
        return view('backend.employee.add_employee');
    }// end method

    public function StoreEmployee(Request $request) {
        $validateData = $request->validate([
            'name' => 'required|max:200',
            'email' => 'required|unique:employees|max:200',
            'phone' => 'required|max:200',
            'address' => 'required|max:400',
            'salary' => 'required|max:200',
            'vacation' => 'required|max:200',
            'experience' => 'required',
            'image' => 'required',
        ],
            ['name.required' => 'This Employee Name Field Is Required']
        );

        if ($request->file('image')) {
            $image = $request->file('image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('upload/employee/');

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
            $save_url = 'upload/employee/' . $name_gen;
        } else {
            $save_url = null; // O establece una imagen por defecto si es obligatorio
        }
/*
        if ($request->file('image')) {
            $image = $request->file('image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
    
            Image::make($image)->resize(300, 300)->save('upload/employee/' . $name_gen);
            $save_url = 'upload/employee/' . $name_gen;
        } else {
            $save_url = null; // O establece una imagen por defecto si es obligatorio
        }
*/
        //Image::make($image)->resize(300,300)->save('upload/employee/'.$name_gen);
        Employee::insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'experience' => $request->experience,
            'salary' => $request->salary,
            'vacation' => $request->vacation,
            'city' => $request->city,
            'image' => $save_url,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Employee Inserted Succesfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.employee')->with($notification);
    }// end method
}
