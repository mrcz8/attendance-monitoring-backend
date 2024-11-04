<?php

namespace App\Providers;

use App\Repository\AttendanceRecordRepositoryInterface;
use App\Repository\ClientRepositoryInterface;
use App\Repository\DepartmentRepositoryInterface;
use App\Repository\Eloquent\AttendanceRecordRepository;
use App\Repository\Eloquent\ClientRepository;
use App\Repository\Eloquent\DepartmentRepository;
use App\Repository\Eloquent\EmployeeRepository;
use App\Repository\Eloquent\LicenseKeyRepository;
use App\Repository\Eloquent\ShiftRepository;
use App\Repository\Eloquent\UserRepository;
use App\Repository\EmployeeRepositoryInterface;
use App\Repository\LicenseKeyRepositoryInterface;
use App\Repository\ShiftRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Services\AttendanceRecordService;
use App\Services\AttendanceRecordServiceInterface;
use App\Services\ClientService;
use App\Services\ClientServiceInterface;
use App\Services\DepartmentService;
use App\Services\DepartmentServiceInterface;
use App\Services\EmployeeService;
use App\Services\EmployeeServiceInterface;
use App\Services\LicenseKeyService;
use App\Services\LicenseKeyServiceInterface;
use App\Services\ShiftService;
use App\Services\ShiftServiceInterface;
use App\Services\UserService;
use App\Services\UserServiceInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        /**
         * Services
         */
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(ClientServiceInterface::class, ClientService::class);
        $this->app->bind(LicenseKeyServiceInterface::class, LicenseKeyService::class);
        $this->app->bind(DepartmentServiceInterface::class, DepartmentService::class);
        $this->app->bind(ShiftServiceInterface::class, ShiftService::class);
        $this->app->bind(EmployeeServiceInterface::class, EmployeeService::class);
        $this->app->bind(AttendanceRecordServiceInterface::class, AttendanceRecordService::class);

        /**
         * Repositories
         */
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(LicenseKeyRepositoryInterface::class, LicenseKeyRepository::class);
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(ShiftRepositoryInterface::class, ShiftRepository::class);
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(AttendanceRecordRepositoryInterface::class, AttendanceRecordRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
