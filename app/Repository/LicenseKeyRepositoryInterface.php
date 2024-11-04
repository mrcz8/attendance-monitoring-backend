<?php

namespace App\Repository;

use App\Models\LicenseKey;
use App\Models\User;
use App\Utility\Paginate;

interface LicenseKeyRepositoryInterface
{
    /**
     * Maximum items to be shown per page
     *
     * @var int MAX_PAGE_ITEMS
     */
    public const MAX_PAGE_ITEMS = 15;

    public function list(array $filters = []): Paginate;

    public function generate($key): LicenseKey;

    public function delete($key): LicenseKey;

    public function onboard($client, $onboard): LicenseKey;
}