<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

final class ManageCentralAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'central:admin
                            {action? : Action to perform (create|list|promote|demote|delete)}
                            {--email= : Target user email address}
                            {--name= : User full name for creation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create, list, promote, demote, or delete Central Platform Admin users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        if (! $action) {
            $action = select(
                label: 'Select central admin management action:',
                options: [
                    'create' => 'Create a new Central Admin user',
                    'list' => 'List all Central Admin users',
                    'promote' => 'Promote an existing user to Central Admin',
                    'demote' => 'Demote a Central Admin to regular user',
                    'delete' => 'Delete a Central Admin user',
                ],
                default: 'create'
            );
        }

        return match ($action) {
            'create' => $this->createAdmin(),
            'list' => $this->listAdmins(),
            'promote' => $this->promoteAdmin(),
            'demote' => $this->demoteAdmin(),
            'delete' => $this->deleteAdmin(),
            default => $this->invalidAction($action),
        };
    }

    private function createAdmin(): int
    {
        $name = $this->option('name') ?? text(
            label: 'Enter admin full name:',
            placeholder: 'Super Admin',
            required: true
        );

        $email = $this->option('email') ?? text(
            label: 'Enter admin email address:',
            placeholder: 'admin@church.test',
            required: true,
            validate: fn (string $value) => match (true) {
                ! filter_var($value, FILTER_VALIDATE_EMAIL) => 'Please enter a valid email address.',
                User::where('email', $value)->exists() => 'A user with this email already exists.',
                default => null,
            }
        );

        $rawPassword = password(
            label: 'Enter admin password:',
            required: true,
            validate: fn (string $value) => strlen($value) < 8 ? 'Password must be at least 8 characters.' : null
        );

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($rawPassword),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->info("Central Admin user [{$user->name}] ({$user->email}) created successfully.");

        return self::SUCCESS;
    }

    private function listAdmins(): int
    {
        $admins = User::where('is_admin', true)->get(['id', 'name', 'email', 'created_at']);

        if ($admins->isEmpty()) {
            $this->warn('No Central Admin users found.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Name', 'Email', 'Created At'],
            $admins->map(fn ($admin) => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'created_at' => $admin->created_at?->toDateTimeString() ?? '-',
            ])->toArray()
        );

        return self::SUCCESS;
    }

    private function promoteAdmin(): int
    {
        $email = $this->option('email') ?? text(
            label: 'Enter email of user to promote:',
            required: true
        );

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email [{$email}] not found.");

            return self::FAILURE;
        }

        if ($user->is_admin) {
            $this->warn("User [{$email}] is already a Central Admin.");

            return self::SUCCESS;
        }

        $user->update(['is_admin' => true]);
        $this->info("User [{$email}] has been promoted to Central Admin.");

        return self::SUCCESS;
    }

    private function demoteAdmin(): int
    {
        $email = $this->option('email') ?? text(
            label: 'Enter email of admin to demote:',
            required: true
        );

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email [{$email}] not found.");

            return self::FAILURE;
        }

        if (! $user->is_admin) {
            $this->warn("User [{$email}] is not a Central Admin.");

            return self::SUCCESS;
        }

        if (User::where('is_admin', true)->count() <= 1) {
            $this->error('Cannot demote the only remaining Central Admin user.');

            return self::FAILURE;
        }

        $user->update(['is_admin' => false]);
        $this->info("User [{$email}] has been demoted to regular user.");

        return self::SUCCESS;
    }

    private function deleteAdmin(): int
    {
        $email = $this->option('email') ?? text(
            label: 'Enter email of admin to delete:',
            required: true
        );

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email [{$email}] not found.");

            return self::FAILURE;
        }

        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            $this->error('Cannot delete the only remaining Central Admin user.');

            return self::FAILURE;
        }

        if (! confirm("Are you sure you want to delete admin user [{$email}]?")) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        $user->delete();
        $this->info("Admin user [{$email}] deleted successfully.");

        return self::SUCCESS;
    }

    private function invalidAction(string $action): int
    {
        $this->error("Invalid action [{$action}]. Allowed actions: create, list, promote, demote, delete.");

        return self::FAILURE;
    }
}
