<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function FinalInvoice(Request $request) {
        $data = array();
        $data['customer_id'] = $request->customer_id;
        $data['order_date'] = $request->order_date;
        $data['order_status'] = $request->order_status;
        $data['total_products'] = $request->total_products;
        $data['sub_total'] = $request->sub_total;
        $data['vat'] = $request->vat;
        $data['invoice_no'] = 'EPOS'.mt_rand(10000000,99999999);
        $data['total'] = $request->total;
        $data['payment_status'] = $request->payment_status;
        $data['pay'] = $request->pay;
        $data['due'] = $request->due;
        $data['created_at'] = Carbon::now();
        
        $order_id = Order::insertGetId($data);
        $contents = Cart::content();

        $pdata = array();
        foreach($contents as $content){
            $pdata['order_id'] = $order_id;
            $pdata['product_id'] = $content->id;
            $pdata['quantity'] = $content->qty;
            $pdata['unitcost'] = $content->price;
            $pdata['total'] = $content->total;

            $insert = OrderDetails::insert($pdata);
        } // end foreach

        $notification = array(
            'message' => 'Order Completed Succesfully',
            'alert-type' => 'success'
        );

        Cart::destroy();

        return redirect()->route('dashboard')->with($notification);


    } // end method

    public function PendingOrder() {
        $orders = Order::where('order_status', 'pending')->get();
        return view('backend.order.pending_order', compact('orders'));
    } // end method

    public function OrderDetails($order_id) {
        $order = Order::where('id', $order_id)->first();

        $orderItem = OrderDetails::with('product')->where('order_id', $order_id)->orderBy('id', 'DESC')->get();

        return view('backend.order.order_details', compact('order','orderItem'));
    } // end method

    public function OrderStatusUpdate(Request $request) {
        $order_id = $request->id;

        $product = OrderDetails::where('order_id', $order_id)->get();

        foreach($product as $item){
            Product::where('id', $item->product_id)->update(['product_store' => DB::raw('product_store-'.$item->quantity)]);
        }

        Order::findOrFail($order_id)->update(['order_status' => 'complete']);

        $notification = array(
            'message' => 'Order Done Succesfully',
            'alert-type' => 'success'
        );

        return redirect()->route('pending.order')->with($notification);
    } // end method

    public function CompleteOrder() {
        $orders = Order::where('order_status', 'complete')->get();
        return view('backend.order.complete_order', compact('orders'));
    } // end method

    public function StockManage(){
        $product = Product::latest()->get();
        return view('backend.stock.all_stock', compact('product'));
    } // end method

    public function OrderInvoice($order_id) {
        $order = Order::where('id', $order_id)->first();

        $orderItem = OrderDetails::with('product')->where('order_id', $order_id)->orderBy('id', 'DESC')->get();

        $pdf = $pdf = Pdf::loadView('backend.order.order_invoice', compact('order', 'orderItem'))->setPaper('a4')->setOption([
            'tempDir' => public_path(),
            'chroot' => public_path(),
        ]);
        return $pdf->download('invoice.pdf');
    }  // end method
}
