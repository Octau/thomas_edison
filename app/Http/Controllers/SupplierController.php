<?php

namespace App\Http\Controllers;

use App\Http\Helpers\RequestHelper;
use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\QueryBuilder\Admin\SupplierQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use Throwable;

class SupplierController extends Controller
{

    public function __construct(private SupplierQueryBuilder $supplierQueryBuilder)
    {
    }

    public function index(Request $request): ResourceCollection
    {
        $suppliers = $this->supplierQueryBuilder->getQueryBuilder($request->boolean('is_active'), $request->input('q'));
        return SupplierResource::collection($suppliers->paginate(RequestHelper::limit($request)))
            ->additional($this->supplierQueryBuilder->getResource($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier): SupplierResource
    {
        return new SupplierResource($supplier);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $request): SupplierResource
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $supplier = new Supplier($validated);
            $supplier->saveOrFail();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
        return (new SupplierResource($supplier))->additional(['message' => __('success.store_supplier_success')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $validated = $request->validated();
        $supplier->fill($validated);
        $supplier->saveOrFail();
        return (new SupplierResource($supplier))
            ->additional(['message' => __('success.update_supplier_success')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        DB::beginTransaction();
        try {
            $supplier->delete();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
        return response()->json([
            'message' => __('success.delete_supplier_success'),
        ]);
    }
}
