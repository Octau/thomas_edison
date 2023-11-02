<?php

namespace App\Http\Controllers;

use App\Exports\GetTransactionExport;
use App\Http\Helpers\RequestHelper;
use App\Http\Resources\Report\TransactionReportResource;
use App\QueryBuilder\Admin\Report\PurchaseReportQueryBuilder;
use App\QueryBuilder\Admin\Report\TransactionReportQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    const MONEY_FORMAT = 'money';

    const PERCENTAGE_FORMAT = 'percentage';

    const NUMERIC_FORMAT = 'numeric';

    const STRING_FORMAT = 'string';

    const DATE_FORMAT = 'date';

    const DEFAULT_AGGREGATE = 'DEFAULT';

    const SUM_AGGREGATE = 'SUM';

    const COUNT_AGGREGATE = 'COUNT';

    const MAX_AGGREGATE = 'MAX';

    const MIN_AGGREGATE = 'MIN';

    const STRING_AGGREGATE = 'STRING_AGG';

    public function __construct(
        private PurchaseReportQueryBuilder $purchaseReportQueryBuilder,
        private TransactionReportQueryBuilder $transactionReportQueryBuilder,
    )
    {
    }

    public function getTransaction(Request $request): ResourceCollection
    {
        $transactions = $this->transactionReportQueryBuilder->getQueryBuilder();

        return TransactionReportResource::collection($transactions->paginate(RequestHelper::limit($request)))
            ->additional($this->transactionReportQueryBuilder->getResource($request));
    }

    public function getTransactionExport(Request $request): BinaryFileResponse|Response
    {
        $query = $this->transactionReportQueryBuilder->getQueryBuilder($request);
        return (new GetTransactionExport($query))->download('get_transactions.xlsx');
    }

    public function getPurchase(Request $request): ResourceCollection
    {
        $transactions = $this->purchaseReportQueryBuilder->getQueryBuilder();

        return TransactionReportResource::collection($transactions->paginate(RequestHelper::limit($request)))
            ->additional($this->purchaseReportQueryBuilder->getResource($request));
    }

    public function getPurchaseExport(Request $request): BinaryFileResponse|Response
    {
        $query = $this->purchaseReportQueryBuilder->getQueryBuilder($request);

        return (new GetTransactionExport($query))->download('get_purchases.xlsx');
    }
}
