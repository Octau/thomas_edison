<?php


namespace App\Models\Common;

use App\Models\Abstract\BaseEnum;

class ActivityLogName extends BaseEnum
{
    public const MASTER_INVENTORY = 'master inventory';

    public const MASTER_SUPPLIER = 'master supplier';

    public const MASTER_CUSTOMER = 'master customer';

    public const PURCHASE = 'purchase';

    public const CASHIER = 'cashier';
}
