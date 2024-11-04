<?php

namespace App\Repository\Eloquent;

use App\Models\LicenseKey;
use App\Repository\Base\BaseRepository;
use App\Repository\LicenseKeyRepositoryInterface;
use App\Utility\Paginate;
use Illuminate\Support\Facades\DB;

class LicenseKeyRepository extends BaseRepository implements LicenseKeyRepositoryInterface
{
    protected array $defaultFilters = [
        /**
         * Pagination
         * The current page of items to get
         */
        'page' => 1,
    ];

    public function __construct(LicenseKey $licenseKey)
    {
        parent::__construct($licenseKey);
    }

    public function list(array $userFilters = []): Paginate
    {
        $items = $this->model->query();
        $filters = array_merge($this->defaultFilters, array_filter($userFilters, fn ($f) => !is_null($f)));

        return new Paginate($items, self::MAX_PAGE_ITEMS, $filters['page'], 'items');
    }

    public function generate($key): LicenseKey
    {
        $licenseKey = new LicenseKey();
        $licenseKey->key = $key;

        DB::transaction(function() use($licenseKey){
            $licenseKey->save();
        });

        return $licenseKey;
    }

    public function delete($key): LicenseKey
    {
        DB::transaction(function() use($key){
            $key->delete();
        });

        return $key;
    }

    public function onboard($client, $onboard): LicenseKey
    {
        DB::transaction(function() use($onboard){
            $onboard->save();
        });

        return $onboard;
    }
}