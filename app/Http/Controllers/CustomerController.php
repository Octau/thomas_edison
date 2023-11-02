<?php

namespace App\Http\Controllers;

use App\Http\Helpers\RequestHelper;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\QueryBuilder\Admin\CustomerQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use Throwable;

class CustomerController extends Controller
{
    public function __construct(private CustomerQueryBuilder $customerQueryBuilder)
    {
    }

    public function index(Request $request): ResourceCollection
    {
        $customers = $this->customerQueryBuilder->getQueryBuilder($request->input('q'));
        return CustomerResource::collection($customers->paginate(RequestHelper::limit($request)))
            ->additional($this->customerQueryBuilder->getResource($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): CustomerResource
    {
        return new CustomerResource($customer);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request): CustomerResource
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $customer = new Customer($validated);
            $customer->saveOrFail();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
        return (new CustomerResource($customer))->additional(['message' => __('success.store_customer_success')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $validated = $request->validated();
        $customer->fill($validated);
        $customer->saveOrFail();
        return (new CustomerResource($customer))
            ->additional(['message' => __('success.update_customer_success')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        DB::beginTransaction();
        try {
            $customer->delete();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
        return response()->json([
            'message' => __('success.delete_customer_success'),
        ]);
    }
}
