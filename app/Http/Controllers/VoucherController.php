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
     * Get available vouchers dengan validation untuk checkout
     * GET /cart/vouchers/available?shipping_method_id=xxx&payment_method_id=yyy
     */
    public function available(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 20);
            $user = auth()->user();
            $cart = $user ? Cart::where('user_id', $user->id)->first() : null;

            // Get shipping & payment methods jika dikirimkan di query
            $shipping = null;
            $payment = null;
            
            if ($request->has('shipping_method_id')) {
                $shipping = \App\Models\ShippingMethod::find($request->shipping_method_id);
            }
            
            if ($request->has('payment_method_id')) {
                $payment = \App\Models\PaymentMethod::find($request->payment_method_id);
            }

            // Get voucher models so we can pass to service
            $vouchers = \App\Models\Voucher::valid()
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            $appliedIds = [];
            if ($cart) {
                $applied = $cart->vouchers ?? [];
                $appliedIds = array_column($applied, 'id');
            }

            $vouchersWithStatus = $vouchers->map(function ($voucher) use ($cart, $shipping, $payment) {
                $elig = $this->voucherService->checkVoucherEligibility(
                    $voucher, 
                    $cart, 
                    $shipping ?? ($cart->selected_shipping_method ?? null), 
                    $payment ?? ($cart->selected_payment_method ?? null)
                );

                // Calculate user's remaining quota for this voucher
                $userUsageCount = \App\Models\VoucherUsage::where('voucher_id', $voucher->id)
                    ->where('user_id', auth()->id())
                    ->where('status', 'used')
                    ->count();
                
                $maxUsagePerUser = $voucher->max_usage_per_user ?? 1;
                $userRemainingQuota = max(0, $maxUsagePerUser - $userUsageCount);

                return [
                    'id' => $voucher->id,
                    'code' => $voucher->code,
                    'name' => $voucher->name,
                    'description' => $voucher->description,
                    'type' => $voucher->type,
                    'type_label' => $voucher->type_label,
                    'value' => $voucher->value,
                    'maximum_discount' => $voucher->maximum_discount,
                    'badge_color' => $voucher->badge_color ?? '#FF6B6B',
                    'image_url' => $voucher->image_url,
                    'minimum_purchase' => $voucher->minimum_purchase ?? 0,
                    'members_only' => $voucher->members_only ?? false,
                    'max_usage_per_user' => $maxUsagePerUser,
                    'is_active' => $voucher->is_active,
                    'is_expired' => $voucher->is_expired,
                    'is_sold_out' => $voucher->is_sold_out,
                    'remaining_quota' => $voucher->remaining_quota,
                    'user_remaining_quota' => $userRemainingQuota,
                    'can_apply' => $elig['is_eligible'],
                    'validation_reasons' => $elig['reasons'],
                    'discount_preview' => $elig['discount_info'],
                ];
            });

            // Exclude already applied vouchers
            $filtered = $vouchersWithStatus->reject(function ($v) use ($appliedIds) {
                return in_array($v['id'], $appliedIds, true);
            })->values();

            // Sort usable first
            $sorted = collect([
                ...$filtered->filter(fn($v) => $v['can_apply'] === true)->values(),
                ...$filtered->filter(fn($v) => $v['can_apply'] === false)->values(),
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
     * Return ALL active vouchers with validation against current cart
     * GET /cart/vouchers/list-with-validation
     */
    public function listWithValidation(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $cart = $user ? Cart::where('user_id', $user->id)->first() : null;

            $vouchers = \App\Models\Voucher::valid()
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get();

            $result = $vouchers->map(function ($voucher) use ($cart) {
                $elig = $this->voucherService->checkVoucherEligibility($voucher, $cart, $cart->selected_shipping_method ?? null, $cart->selected_payment_method ?? null);

                return [
                    'basic' => [
                        'id' => $voucher->id,
                        'code' => $voucher->code,
                        'name' => $voucher->name,
                        'type' => $voucher->type,
                        'value' => $voucher->value,
                        'badge_color' => $voucher->badge_color ?? '#FF6B6B',
                        'image_url' => $voucher->image_url,
                        'description' => $voucher->description,
                    ],
                    'rules' => [
                        'minimum_purchase' => $voucher->minimum_purchase ?? 0,
                        'members_only' => $voucher->members_only ?? false,
                        'max_usage_per_user' => $voucher->max_usage_per_user ?? 1,
                    ],
                    'status' => [
                        'is_active' => $voucher->is_active,
                        'is_expired' => $voucher->is_expired,
                        'remaining_quota' => $voucher->remaining_quota,
                    ],
                    'eligibility' => [
                        'can_apply' => $elig['is_eligible'],
                        'validation_reasons' => $elig['reasons'],
                        'discount_preview' => $elig['discount_info'],
                    ],
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /cart/vouchers/check-eligibility-batch
     * Accepts: voucher_ids: array, optional shipping_method_id, payment_method_id
     */
    public function checkEligibilityBatch(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'voucher_ids' => 'required|array',
                'voucher_ids.*' => 'string',
                'shipping_method_id' => 'nullable|string',
                'payment_method_id' => 'nullable|string',
            ]);

            $user = auth()->user();
            $cart = $user ? Cart::where('user_id', $user->id)->first() : null;

            $shipping = null;
            $payment = null;

            if ($request->shipping_method_id) {
                $shipping = \App\Models\ShippingMethod::find($request->shipping_method_id);
            }

            if ($request->payment_method_id) {
                $payment = \App\Models\PaymentMethod::find($request->payment_method_id);
            }

            $results = [];
            foreach ($request->voucher_ids as $vid) {
                $voucher = \App\Models\Voucher::find($vid);
                if (!$voucher) {
                    $results[] = [
                        'id' => $vid,
                        'is_eligible' => false,
                        'validation_reasons' => ['Voucher tidak ditemukan.'],
                    ];
                    continue;
                }

                $elig = $this->voucherService->checkVoucherEligibility($voucher, $cart, $shipping, $payment);

                $results[] = array_merge([
                    'id' => $voucher->id,
                    'code' => $voucher->code,
                    'name' => $voucher->name,
                ], $elig);
            }

            return response()->json([
                'success' => true,
                'data' => $results,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
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

            $appliedVouchers = $cart->getAppliedVouchers()->map(function($v) {
                $va = (array) $v;
                return [
                    'id' => $va['id'] ?? null,
                    'name' => $va['name'] ?? null,
                    'code' => $va['code'] ?? null,
                    'description' => $va['description'] ?? null,
                    'image_url' => $va['image_url'] ?? null,
                    'type' => $va['type'] ?? null,
                    'value' => $va['value'] ?? null,
                    'minimum_purchase' => $va['minimum_purchase'] ?? 0,
                ];
            })->toArray();

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
