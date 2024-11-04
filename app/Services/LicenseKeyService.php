<?php

namespace App\Services;

use App\Models\LicenseKey;
use App\Models\User;
use App\Repository\LicenseKeyRepositoryInterface;
use App\Utility\Paginate;
use SemiorbitSerial\SerialNumber;

class LicenseKeyService implements LicenseKeyServiceInterface
{
    protected $licenseKeyRepository;

    public function __construct(LicenseKeyRepositoryInterface $licenseKeyRepositoryInterface)
    {
        $this->licenseKeyRepository = $licenseKeyRepositoryInterface;
    }

    public function list(array $filters = []): Paginate
    {
        return $this->licenseKeyRepository->list($filters);
    }

    public function generate(): LicenseKey
    {
        $key = SerialNumber::Generate('-');
        return $this->licenseKeyRepository->generate($key);
    }

    public function delete($id): LicenseKey
    {
        $key = LicenseKey::find($id);
        if ($key && $key->user_id) {
            throw new \Exception('This license key is associated with a user and cannot be deleted.');
        }

        return $this->licenseKeyRepository->delete($key);
    }

    public function onboard($client, $key): LicenseKey
    {
        $licenseKey = LicenseKey::where('key', $key)->first();

        if (!$licenseKey) {
            throw new \Exception("License key not found.");
        }

        if (!is_null($licenseKey->user_id)) {
            throw new \Exception("This license key is already use.");
        }

        $licenseKey->user_id = $client->id;

        return $this->licenseKeyRepository->onboard($client, $licenseKey);
    }
}