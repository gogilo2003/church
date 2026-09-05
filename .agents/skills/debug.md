# Skill Playbook: Debug Issues

## Common Diagnosis Workflows

### 1. Tenancy Connection Errors
- Check if request hostname matches tenant domain database.
- Inspect `config/tenancy.php` central domain definitions.

### 2. Inertia Page Render Warnings
- Verify prop names match between Controller `$props` array and Vue `defineProps<{ ... }>()`.
