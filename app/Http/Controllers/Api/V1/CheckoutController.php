<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;
use App\Models\Merchant;
use App\Models\Customer;
use App\Http\Resources\V1\CartWithDetailsResource;
use Illuminate\Support\Facades\Redirect;
use Validator;

class CheckoutController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.role:user');
    }

    public function processCheckout(Request $request, $cart_id)
    {
        $cart = Cart::find($cart_id);
        $cartDetail = CartDetail::where('cart_id', $cart->id)->get();

        if ($cart === null ) {
            return $this->errorResponse(
                Response::HTTP_NOT_FOUND . " Not Found",
                "Cart with id " . $cart_id . " is not found",
                Response::HTTP_NOT_FOUND
            );
        }

        if (count($cartDetail) <= 0) {
            return $this->errorResponse(
                Response::HTTP_BAD_REQUEST . " Bad Request",
                "Tidak ada item didalam keranjang",
                Response::HTTP_BAD_REQUEST
            );
        }

        $validator = Validator::make($request->all(), [
            'additional_message' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse(
                Response::HTTP_UNPROCESSABLE_ENTITY . " Unprocessable Content",
                $validator->errors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
         }

        $cartItems = $cart->cartDetails;
        $merchant = Merchant::find($cart->merchant_id);
        $additionalMessage = $request->additional_message;
        $customer = Customer::find($cart->customer_id);

        $message = $this->buildOrderMessage($cart, $merchant, $additionalMessage, $customer);

        $whatsAppUrl = $this->getWhatsAppUrl($merchant, $message);

        $data = [
            'status' => Response::HTTP_OK . ' OK',
            'message' => "Your request has been processed successfully",
            'data' => [
                'redirect_link' => $whatsAppUrl
            ]
        ];

        return response()->json($data, Response::HTTP_OK);
    }

    private function buildOrderMessage($cart, $merchant, $additionalMessage, $customer)
    {
        $message = "Halo " . $merchant->merchant_name . ", \nBerikut ini adalah pemesanan dari saya\n\n";

        // informasi pembeli
        $message .= "Nama Pembeli: " . $customer->fullname . "\n";
        $message .= "No. HP: " . $customer->phone_number . "\n";
        $message .= "Alamat: " . $customer->address . ", Kab. " . $customer->city . ", Prov. " . $customer->province . ", " . $customer->state . "\n";
        $message .= "Kode Pos: " . $customer->postal_code . "\n\n";

        // daftar item
        $message .= "*Daftar Pesanan:*\n\n";
        $counter = 1;
        foreach ($cart->cartDetails as $item => $value) {
            $product = Product::find($value->product_id);

            $message .= $counter . ". ";
            $message .= "\tNama Produk: " . $product->name . "\n";
            $message .= "\tJumlah: " . $value->quantity . "\n";
            $message .= "\tSubtotal: " . $value->price . "\n\n";

            $counter += 1;
        }

        // catatan
        if ($additionalMessage == null) {
            $additionalMessage = "(Tidak ada)";
        }
        $message .= "*Catatan:*\n" . $additionalMessage . "\n\n";

        //penutup
        $message .= "Silakan konfirmasi pesanan ini dan berikan informasi lebih lanjut mengenai pembayaran dan pengiriman. \n\nTerima kasih,\n" . $customer->fullname;

        return $message;
    }

    private function getWhatsAppUrl($merchant, $message)
    {
        $encodedMessage = urlencode($message);

        $whatsAppBaseUrl = 'https://wa.me/'.$merchant->wa_number;
        $whatsAppUrl = $whatsAppBaseUrl . "?text=" . $encodedMessage;

        return $whatsAppUrl;
    }
}
