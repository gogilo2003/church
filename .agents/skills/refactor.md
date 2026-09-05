# Skill Playbook: Refactor Code

## Steps
1. Ensure existing Pest tests pass before modifying code.
2. Extract direct database logic from Controllers into Service & Repository classes.
3. Replace inline `$request->validate()` calls with FormRequest classes.
4. Run `npx vue-tsc --noEmit` and `vendor/bin/pest` after refactoring.
