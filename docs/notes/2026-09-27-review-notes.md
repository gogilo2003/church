# Review Notes

I am conducting user test by interacting with the system focusing on specific components'

## Testing onboarding flow

### Issue 1

```php
App\DataTransferObjects\OnboardingData {#1178 ▼ // app/Services/Central/TenantProvisioningService.php:19
  +churchName: "ACME Church"
  +subdomain: "acme"
  +adminName: "Jane Doe"
  +adminEmail: "jane.doe@acme.com"
  +adminPassword: "password"
  +phone: "+254790584171"
  +isCentralAdminCreated: false
}
```

The tenant provision method receives the data correctly but what is stored in the central_database.tenants.data field omits some data as shown below

```json
{"updated_at":"2026-09-27 01:41:29","created_at":"2026-09-27 01:41:29","tenancy_db_name":"church_elck"}
```
and also the user created for the tenant seems to be coming from `Database\Seeders\TenantDefaultSeeder`, the user information provided by for the tenant seems to disappear

### Issue 2

The tenant is created, domains created and database correctly created and migrated for the tenant but I get this error

PDOException
vendor/laravel/framework/src/Illuminate/Database/Concerns/ManagesTransactions.php:54
There is no active transaction

LARAVEL
13.23.0
PHP
8.3.33
UNHANDLED
CODE 0
500
POST
https://church.test/admin/tenants

Exception trace
2 vendor frames

Illuminate\Database\Connection->transaction()
app/Services/Central/TenantProvisioningService.php:20

15     */
16    public function provision(OnboardingData $data): Tenant
17    {
18        $connection = config('tenancy.database.central_connection') ?? config('database.default');
19
20        return DB::connection($connection)->transaction(function () use ($data) {
21            $tenant = Tenant::create([
22                'id' => $data->subdomain,
23                'data' => [
24                    'church_name' => $data->churchName,
25                    'admin_name' => $data->adminName,
26                    'admin_email' => $data->adminEmail,
27                    'admin_password' => bcrypt($data->adminPassword),
28                    'phone' => $data->phone,
29                    'is_central_admin_created' => $data->isCentralAdminCreated,
30                ],
31            ]);
32
App\Services\Central\TenantProvisioningService->provision()
app/Http/Controllers/Central/Admin/TenantManagementController.php:35

### On Boarding Flow

The expected provisioning flow should be
Step 1: Provide the basic details initialize the tenant database, create tenant admin user
Step 2: Define the church structure choose the levels. e.g HQ -> Dioceses -> Perishes -> Church/Congregation; choose the numbers and choose to provide details of the structure right away or later(the names of the dioceses, perishes etc) the provision of the names of the level organisation items is optional at provisioning

## Organizational Hierarchy

The Defined Organizational Levels are created but not editable/deletable and hence can not be corrected in case of a mistake. The same to Organizational Units & Branches
We can also consider a more graphical approach or option of managing this structure where once I have the dioceses in place say Central, Western Kenya, Kisii, Kisumu etc I can just click on say Kisii and Choose Add a Parish or Manage Parishes for kisii and then I can manage this individually on a dialog or add in a list

