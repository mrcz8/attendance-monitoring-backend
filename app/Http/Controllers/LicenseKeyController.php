<?php

namespace App\Http\Controllers;

use App\Models\LicenseKey;
use App\Modules\Message\Message;
use App\Services\LicenseKeyServiceInterface;
use Illuminate\Http\Request;

class LicenseKeyController extends Controller
{
    protected $licenseKeyService;

    public function __construct(LicenseKeyServiceInterface $licenseKeyServiceInterface)
    {
        $this->licenseKeyService = $licenseKeyServiceInterface;
    }

    public function list(Request $request, Message $message)
    {
        $page = $request->query('page', null);

        $filters = [
            'page' => $page,
        ];

        $paginatedItems = $this->licenseKeyService->list($filters);

        $message->setContent(200, 'Items retrieved', '', $paginatedItems->toArray());

        return $message->render();
    }

    public function generate(Message $message)
    {
        $key = $this->licenseKeyService->generate();

        if ($key instanceof LicenseKey) {
            $message->setContent(201, 'License key created', '', [
                'Key' => $key
            ]);
        } else {
            $message->setContent(400, 'License key create failed');
        }

        return $message->render();
    }

    public function delete($id, Message $message)
    {
        $key = $this->licenseKeyService->delete($id);

        if ($key instanceof LicenseKey) {
            $message->setContent(200, 'License key deleted', '', [
                'Key' => $key
            ]);
        } else {
            $message->setContent(400, 'License key delete failed');
        }

        return $message->render();
    }
}
