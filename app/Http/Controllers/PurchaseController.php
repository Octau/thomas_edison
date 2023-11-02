<?php

namespace App\Http\Controllers;

use App\Http\Helpers\RequestHelper;
use App\Http\Helpers\SpatieActivityLogHelper;
use App\Http\Requests\PurchaseRequest;
use App\Http\Resources\PurchaseLiteResource;
use App\Http\Resources\PurchaseResource;
use App\Models\Common\ActivityLogName;
use App\Models\Inventory;
use App\Models\Purchase;
use App\QueryBuilder\Admin\PurchaseQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class PurchaseController extends Controller
{
    private function generateCode(): string
    {
        do $code = strtoupper(Str::random(8));
        while (Purchase::query()->where('code', '=', $code)->exists());

        return $code;
    }

    function replaceItemValue($currentData){
        $currentData['id'] = $currentData['item']['id'] ?? null;
        $currentData['name'] = $currentData['item']['name'];
//        $currentData['note'] = $currentData['item']['note'];
        $currentData['buy_price'] = $currentData['item']['buy_price'];
        $currentData['sell_price'] = $currentData['item']['sell_price'];
        $currentData['min_sell_price'] = $currentData['item']['min_sell_price'];
        $currentData['type'] = $currentData['item']['type'];
        $currentData['amount'] = $currentData['item']['amount'];
        unset($currentData['item']);
        return $currentData;
    }

    public function __construct(private PurchaseQueryBuilder $purchaseQueryBuilder)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $purchases = $this->purchaseQueryBuilder->getQueryBuilder($request->input('q'));
        return PurchaseLiteResource::collection($purchases->paginate(RequestHelper::limit($request)))
            ->additional($this->purchaseQueryBuilder->getResource($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        return new PurchaseResource($purchase);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PurchaseRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $purchase = SpatieActivityLogHelper::logWithinBatch(
                ActivityLogName::PURCHASE,
                ActivityLogName::PURCHASE,
                function () use ($validated): Purchase {
                    for ($i = 0; $i < count($validated['items']); $i++){
                        $currentData = $validated['items'][$i];
                        if($currentData['type'] === 'new') {
                            $currentData = $this->replaceItemValue($currentData);
                            $inventory = new Inventory($currentData);
                            $inventory->saveOrFail();
                            $validated['items'][$i]['id'] = $inventory->id;
                        } else {
                            $currentData = $this->replaceItemValue($currentData);
                            $inventory = Inventory::findOrFail($currentData['id']);
                            $validated['items'][$i]['item']['amount'] = $currentData['amount'];
                            $currentData['amount'] += $inventory->amount;
                            $inventory->fill($currentData);
                            $inventory->saveOrFail();
                        }
                    }
                    $validated['created_by'] = Auth::user()->id;
                    $validated['code'] = $this->generateCode();
                    $purchase = new Purchase($validated);
                    $purchase->saveOrFail();
                    return $purchase;
                }
            );

            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return (new PurchaseResource($purchase))->additional(['message' => __('success.store_purchase_success')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        $validated = $request->validated();
        $purchase->fill($validated);
        $purchase->saveOrFail();
        return (new PurchaseResource($purchase))
            ->additional(['message' => __('success.update_purchase_success')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        DB::beginTransaction();
        try {
            $purchase->delete();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
        return response()->json([
            'message' => __('success.delete_purchase_success'),
        ]);
    }
}
