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
     * Tambah voucher ke cart (support by ID atau code)
     */
    public function add(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'voucher_code' => 'nullable|string|max:50',
                'voucher_id' => 'nullable|string|uuid',
            ]);

            if (!$request->voucher_code && !$request->voucher_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher code atau ID harus diisi.',
                ], 422);
            }

            $cart = Cart::where('user_id', auth()->id())->first();
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang tidak ditemukan.',
                ], 404);
            }

            // Jika kirim voucher_id, cari code terlebih dahulu
            if ($request->voucher_id) {
                $voucher = Voucher::find($request->voucher_id);
                if (!$voucher) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Voucher tidak ditemukan.',
                    ], 404);
                }
                $voucherCode = $voucher->code;
            } else {
                $voucherCode = $request->voucher_code;
            }

            $result = $this->voucherService->addVoucher($cart, $voucherCode);
            $cart->refresh();

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'items' => $cart->items()->with('product')->get()->map(fn($item) => [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'product_image_url' => $item->product->thumbnail ?? null,
                        'quantity' => $item->quantity,
                        'product_price' => $item->product_price,
                        'original_price' => $item->original_price,
                        'subtotal' => $item->subtotal,
                        'discount_amount' => $item->discount_amount,
                    ]),
                    'vouchers' => $cart->getAppliedVouchers()->map(fn($v) => [
                        'id' => $v->id,
                        'name' => $v->name,
                        'code' => $v->code,
                    ])->toArray(),
                    'summary' => [
                        'total_items' => $cart->total_items,
                        'total_quantity' => $cart->total_quantity,
                        'subtotal' => $cart->subtotal,
                        'discount_amount' => $cart->total_discount_amount ?? 0,
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
                    'items' => $cart->items()->with('product')->get()->map(fn($item) => [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'product_image_url' => $item->product->thumbnail ?? null,
                        'quantity' => $item->quantity,
                        'product_price' => $item->product_price,
                        'original_price' => $item->original_price,
                        'subtotal' => $item->subtotal,
                        'discount_amount' => $item->discount_amount,
                    ]),
                    'vouchers' => $cart->getAppliedVouchers()->map(fn($v) => [
                        'id' => $v->id,
                        'name' => $v->name,
                        'code' => $v->code,
                    ])->toArray(),
                    'summary' => [
                        'total_items' => $cart->total_items,
                        'total_quantity' => $cart->total_quantity,
                        'subtotal' => $cart->subtotal,
                        'discount_amount' => $cart->total_discount_amount ?? 0,
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
     * List available vouchers dengan detailed info (can_apply, reasons, etc)
     */
    public function available(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 20);
            $user = auth()->user();
            $cart = $user ? Cart::where('user_id', $user->id)->first() : null;

            $vouchers = $this->voucherService->getAvailableVouchers($limit);

            // Enrich with applicability check
            $vouchersWithStatus = $vouchers->map(function ($voucher) use ($cart, $user) {
                $canApply = true;
                $reasons = [];

                if ($cart && $cart->items) {
                    // Check kuota
                    if (!$voucher['quota'] || $voucher['used_count'] >= $voucher['quota']) {
                        $canApply = false;
                        $reasons[] = 'Kuota voucher sudah habis';
                    }

                    // Check minimum purchase
                    if ($voucher['minimum_purchase'] && $cart->subtotal < $voucher['minimum_purchase']) {
                        $canApply = false;
                        $reasons[] = 'Minimal pembelian Rp' . number_format($voucher['minimum_purchase'], 0, ',', '.');
                    }

                    // Check user eligibility
                    if ($voucher['members_only'] && !$user) {
                        $canApply = false;
                        $reasons[] = 'Voucher ini hanya untuk member';
                    }

                    // Check user usage limit
                    if ($user) {
                        $usageCount = \App\Models\VoucherUsage::where('voucher_id', $voucher['id'])
                            ->where('user_id', $user->id)
                            ->where('status', 'used')
                            ->count();

                        $maxUsage = $voucher['max_usage_per_user'] ?? 1;
                        if ($usageCount >= $maxUsage) {
                            $canApply = false;
                            $reasons[] = 'Anda sudah mencapai batas penggunaan voucher ini';
                        }
                    }
                }

                return array_merge($voucher, [
                    'can_apply' => $canApply,
                    'reasons' => $reasons,
                    'quota' => $voucher['remaining_quota'] ?? 0,
                    'used_count' => $voucher['used_count'] ?? 0,
                ]);
            });

            // Sort: usable first, then not usable
            $sorted = collect([
                ...$vouchersWithStatus->filter(fn($v) => $v['can_apply'] === true)->values(),
                ...$vouchersWithStatus->filter(fn($v) => $v['can_apply'] === false)->values(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $sorted->values()->toArray(),
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
