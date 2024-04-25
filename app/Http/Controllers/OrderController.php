<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductInventory;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

    public function momo_payment(Request $request)
    {
        header('Content-type: application/json');
        $total_all =  $request->input('total_all');
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo = "Thanh toán qua MoMo";
        $amount = $total_all;
        $orderId = time() . "";
        $redirectUrl = "http://localhost:5173/checkout";
        $ipnUrl = "http://localhost:5173/checkout";
        $extraData = "";

        $requestId = time() . "";
        $requestType = "payWithATM";
        // $extraData = ($_POST["extraData"] ? $_POST["extraData"] : "");
        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;

        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        $result = $this->execPostRequest($endpoint, json_encode($data));

        $jsonResult = json_decode($result, true);

        return response()->json(['url' => [$jsonResult['payUrl']]], 200);
    }

    public function add_order(Request $request)
    {
        $cartCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 7);
        Order::create([
            'user_id' => auth()->user()->id,
            'recipient_name' => $request->input('name'),
            'cart_code' => $cartCode,
            'province' => $request->input('province'),
            'district' => $request->input('district'),
            'ward' => $request->input('ward'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'total_all' => $request->input('total_all'),
            'payment_method' => $request->input('payment_method'),
            'order_status' => 1,
            "payment_status" => 0
        ]);

        $checkouts = $request->input('checkouts');
        foreach ($checkouts as $checkout) {
            $color_id = $checkout['color_id'];
            $size_id = $checkout['size_id'];
            $product_id = $checkout['pid'];
            $quantity = $checkout['quantity'];

            ProductInventory::where('color_id', $color_id)
                ->where('size_id', $size_id)
                ->where('product_id', $product_id)
                ->decrement('quantity_buy', $quantity);

            OrderDetail::create([
                'product_id' => $checkout['pid'],
                'cart_code' => $cartCode,
                'product_name' => $checkout['p_name'],
                'size_id' => $size_id,
                'color_id' => $color_id,
                'price' => $checkout['p_price'] - $checkout['p_price'] * $checkout['discount'] / 100,
                'quantity' => $quantity,
                'total_all' => ($checkout['p_price'] - $checkout['p_price'] * $checkout['discount'] / 100) * $quantity
            ]);
        }

        Cart::where('user_id', auth()->user()->id)->delete();

        return response()->json(['data' => "Đặt hàng thành công"], 201);
    }

    public function get_order()
    {
        $userId = auth()->user()->id;
        $orders = Order::where('user_id', $userId)->get();
        return response()->json(['data' => $orders], 200);
    }

    public function get_all_orders()
    {
        $orders = Order::all();
        return response()->json(['data' => $orders], 200);
    }

    public function get_order_details($code)
    {
        $orderDetails = OrderDetail::where('cart_code', $code)->get();
        return response()->json(['data' => $orderDetails], 200);
    }

    public function updateOrder(Request $request, $code)
    {
        // Tìm đơn hàng cần cập nhật
        $order = Order::where('cart_code', $code)->first();

        // Kiểm tra xem đơn hàng có tồn tại không
        if (!$order) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng'], 404);
        }

        // Lấy chỉ các trường được gửi trong yêu cầu
        $updateData = $request->only([
            'name',
            'province',
            'district',
            'ward',
            'address',
            'phone',
            'total_all',
            'payment_method',
            'order_status',
            'payment_status'
        ]);

        // Cập nhật các trường của đơn hàng
        $order->update($updateData);

        return response()->json(['message' => 'Đơn hàng cập nhật thành công'], 200);
    }
}
