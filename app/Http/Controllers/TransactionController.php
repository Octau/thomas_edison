<?php

namespace App\Http\Controllers;

use App\Http\Helpers\RequestHelper;
use App\Http\Helpers\SpatieActivityLogHelper;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionLiteResource;
use App\Http\Resources\TransactionResource;
use App\Models\Common\ActivityLogName;
use App\Models\Inventory;
use App\Models\Transaction;
use App\QueryBuilder\Admin\TransactionQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class TransactionController extends Controller
{

    private function generateCode(): string
    {
        do $code = strtoupper(Str::random(8));
        while (Transaction::query()->where('code', '=', $code)->exists());

        return $code;
    }

    public function __construct(private TransactionQueryBuilder $transactionQueryBuilder)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $transactions = $this->transactionQueryBuilder->getQueryBuilder($request->input('q'));
        return TransactionLiteResource::collection($transactions->paginate(RequestHelper::limit($request)))
            ->additional($this->transactionQueryBuilder->getResource($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $transaction = SpatieActivityLogHelper::logWithinBatch(
                ActivityLogName::CASHIER,
                ActivityLogName::CASHIER,
                function () use ($validated): Transaction {
                    for ($i = 0; $i < count($validated['items']); $i++) {
                        $currentData = $validated['items'][$i];
                        $inventoryData = Inventory::findOrFail($currentData['inventory_id']);

                        if($inventoryData->amount < $currentData['qty']) {
                            throw ValidationException::withMessages([
                                'items.'.$i.'.qty' => __('validation.lte.numeric',
                                    ['attribute' => 'qty',
                                        'value'=> 1
                                    ]
                                ),
                            ]);
                        }
                        $inventoryData->amount = $inventoryData->amount - $currentData['qty'];
                        $inventoryData->saveOrFail();
                        $validated['items'][$i]['inventory'] = [
                            'id'    => $inventoryData->id,
                            'name'  => $inventoryData->name,
                        ];
                        unset($validated['items'][$i]['inventory_id']);
                    }
                    $validated['created_by'] = Auth::user()->id;
                    $validated['code'] = $this->generateCode();
                    $transaction = new Transaction($validated);
                    $transaction->saveOrFail();
                    return $transaction;
                }
            );

            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return (new TransactionResource($transaction))
            ->additional(['message' => __('success.store_transaction_success')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionRequest $request, Transaction $transaction)
    {
        $validated = $request->validated();
        $transaction->fill($validated);
        $transaction->saveOrFail();
        return (new TransactionResource($transaction))
            ->additional(['message' => __('success.update_transaction_success')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        DB::beginTransaction();
        try {
            $transaction->delete();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
        return response()->json([
            'message' => __('success.delete_transaction_success'),
        ]);
    }
}
