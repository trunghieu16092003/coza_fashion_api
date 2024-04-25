<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::all();
        return response()->json(['data' => $coupons], 200);
    }

    public function getCouponById($id)
    {
        $coupon = Coupon::find($id);
        return response()->json(['data' => [$coupon]], 200);
    }

    public function addCoupon(Request $request)
    {
        Coupon::create([
            'name' => $request->input('name'),
        ]);
        return response()->json(['message' => "Thêm voucher thành công"], 201);
    }

    public function updateCoupon(Request $request, $id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }


        if ($request->has('data')) {

            $name = $request->input('data.name');
            $coupon->update([
                'name' => $name,
            ]);

            return response()->json(['message' => 'Coupon updated successfully'], 200);
        } else {
            return response()->json(['message' => 'Invalid data format. Missing name field.'], 400);
        }
    }



    public function deleteCoupon($id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }

        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted successfully'], 200);
    }

    public function search($search)
    {
        $coupons = Coupon::where('name', 'like', '%' . $search . '%')
            ->orWhere('id', $search)
            ->get();

        if ($coupons->isEmpty()) {
            return response()->json(['message' => 'No matching coupons found'], 404);
        }

        return response()->json(['data' => $coupons], 200);
    }
}
