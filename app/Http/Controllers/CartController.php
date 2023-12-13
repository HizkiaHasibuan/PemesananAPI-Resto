<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addtocart(Request $request, $id)
    {
        $data['carts'] = Cart::all();
        $data['totalPrice'] = 0;
        $stockReady = true;

        // dd($request->qty);
        foreach ($data['carts'] as $cart) {
            $data['totalPrice'] +=  $cart->menu->harga * $cart->qty;
        }

        $validator = Validator::make($request->all(), [
            'qty' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response([
                'status' => 'error',
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $menu = Menu::find($id);
        if ($menu->stok < $request->qty ) {
            return response([
                'status' => 'error',
                'errors' => 'Stok tidak cukup'
            ], 422);
        }

        $carts = DB::table('carts')
                ->where('menu_id', '=', $id)
                ->first();

        if ( is_null($carts)) {
            Cart::create([
                'menu_id' => $id,
                'qty' => $request->qty
            ]);
        } else {
            Cart::where('menu_id', $id)->update([
                'qty' => $request->qty
            ]);
        }
        return response([
            'status' => true,
            'message' => 'Menu added to cart'
        ], 200);
    }

    public function deletecart($id)
    {
        try {
            $cartItem = DB::table('carts')
            ->where('id', '=', $id)
            ->first();
            if (is_null($cartItem)) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Data tidak ditemukan.'
                ], 404);
            }
            Cart::destroy($id);
            return response([
                'status' => true,
                'message' => 'Cart item successfully deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => 'Terjadi kesalahan saat menghapus data.'
            ], 500);
        }
    }
}
