# Skill Playbook: Review Code

## Checklist
1. **Tenancy Isolation**: Confirm no central/tenant database queries cross boundary.
2. **Type Safety**: Verify `declare(strict_types=1);` in PHP files and no `any` in TypeScript.
3. **Architecture**: Confirm Controllers only delegate to Services & FormRequests.
4. **Security**: Ensure Policy authorization is triggered on all mutating actions.
5. **Testing**: Confirm Pest test coverage accompanies new backend features.
