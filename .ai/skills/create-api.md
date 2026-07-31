# Skill Playbook: Create API Endpoint

## API Endpoint Standard
- Place controllers in `app/Http/Controllers/Api/Tenant/`.
- Return `JsonResponse` or API Resource objects (`app/Http/Resources/`).
- Use Sanctum token authentication for external access.
