<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class VoucherController extends Controller
{
    private VoucherService $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    /**
     * Tambah voucher ke cart
     */
    public function add(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'voucher_code' => 'required|string|max:50',
            ]);

            $cart = Cart::where('user_id', auth()->id())->first();
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang tidak ditemukan.',
                ], 404);
            }

            $result = $this->voucherService->addVoucher($cart, $request->voucher_code);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'vouchers' => $cart->getAppliedVouchers()->toArray(),
                    'total_discount' => $cart->total_discount_amount,
                    'total_shipping_discount' => $cart->total_shipping_discount,
                    'cart_summary' => [
                        'subtotal' => $cart->subtotal,
                        'discount' => $cart->total_discount_amount,
                        'final_subtotal' => $cart->final_subtotal,
                    ],
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['voucher'][0] ?? 'Voucher tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus voucher dari cart
     */
    public function remove(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'voucher_id' => 'required|string|uuid',
            ]);

            $cart = Cart::where('user_id', auth()->id())->first();
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang tidak ditemukan.',
                ], 404);
            }

            $result = $this->voucherService->removeVoucher($cart, $request->voucher_id);

            $cart->refresh();

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'vouchers' => $cart->getAppliedVouchers()->toArray(),
                    'total_discount' => $cart->total_discount_amount,
                    'total_shipping_discount' => $cart->total_shipping_discount,
                    'cart_summary' => [
                        'subtotal' => $cart->subtotal,
                        'discount' => $cart->total_discount_amount,
                        'final_subtotal' => $cart->final_subtotal,
                    ],
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List available vouchers untuk display
     */
    public function available(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $vouchers = $this->voucherService->getAvailableVouchers($limit);

            return response()->json([
                'success' => true,
                'data' => $vouchers,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current applied vouchers in cart
     */
    public function current(Request $request): JsonResponse
    {
        try {
            $cart = Cart::where('user_id', auth()->id())->first();
            
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang tidak ditemukan.',
                ], 404);
            }

            $appliedVouchers = $cart->getAppliedVouchers()->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'vouchers' => $appliedVouchers,
                    'can_add_more' => $cart->canAddMoreVouchers(),
                    'total_discount' => $cart->total_discount_amount,
                    'total_shipping_discount' => $cart->total_shipping_discount,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate voucher sebelum apply
     */
    public function validate(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'voucher_code' => 'required|string|max:50',
            ]);

            $cart = Cart::where('user_id', auth()->id())->first();
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang tidak ditemukan.',
                ], 404);
            }

            // Try add tanpa benar-benar save
            $voucher = Voucher::query()
                ->valid()
                ->where('code', strtoupper($request->voucher_code))
                ->first();

            if (!$voucher) {
                throw ValidationException::withMessages([
                    'voucher' => 'Voucher tidak ditemukan atau tidak aktif.'
                ]);
            }

            $discountData = [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'name' => $voucher->name,
                'type' => $voucher->type,
                'type_label' => $voucher->type_label,
                'value' => $voucher->value,
                'label' => $voucher->label,
                'badge_color' => $voucher->badge_color,
                'is_combinable' => $voucher->is_combinable,
                'is_sold_out' => $voucher->is_sold_out,
                'remaining_quota' => $voucher->remaining_quota,
            ];

            return response()->json([
                'success' => true,
                'data' => $discountData,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['voucher'][0] ?? 'Voucher tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear all vouchers
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $cart = Cart::where('user_id', auth()->id())->first();
            
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang tidak ditemukan.',
                ], 404);
            }

            $cart->clearVoucher();

            return response()->json([
                'success' => true,
                'message' => 'Semua voucher berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
