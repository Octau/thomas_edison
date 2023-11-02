<?php

namespace App\Http\Controllers;

use App\Http\Helpers\RequestHelper;
use App\Http\Requests\InventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\QueryBuilder\Admin\InventoryQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{

    public function __construct(private InventoryQueryBuilder $inventoryQueryBuilder)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection
    {
        $inventories = $this->inventoryQueryBuilder->getQueryBuilder($request->input('q'), $request->input('min_amount'));
        return InventoryResource::collection($inventories->paginate(RequestHelper::limit($request)))
            ->additional($this->inventoryQueryBuilder->getResource($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        return new InventoryResource($inventory);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InventoryRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $inventory = new Inventory($validated);
            $inventory->saveOrFail();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return (new InventoryResource($inventory))->additional(['message' => __('success.store_inventory_success')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InventoryRequest $request, Inventory $inventory)
    {
        $validated = $request->validated();
        $inventory->fill($validated);
        $inventory->saveOrFail();
        return (new InventoryResource($inventory))
            ->additional(['message' => __('success.update_inventory_success')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        DB::beginTransaction();
        try {
            $inventory->delete();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
        return response()->json([
            'message' => __('success.delete_inventory_success'),
        ]);
    }
}
