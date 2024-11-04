<?php

namespace App\Services;

use App\Models\LicenseKey;
use App\Models\User;
use App\Utility\Paginate;

interface LicenseKeyServiceInterface
{
    public function list(array $filters = []): Paginate;

    public function generate(): LicenseKey;

    public function delete($id): LicenseKey;

    public function onboard($client, $key): LicenseKey;
}