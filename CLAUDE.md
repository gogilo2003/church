# Claude Code Guidelines

This repository uses [`AGENTS.md`](file:///home/ogilo/Projects/church/current/AGENTS.md) as the canonical instructions for all AI coding assistants.

Always read and strictly adhere to:
1. **Canonical Guidelines & Invariants**: [`AGENTS.md`](file:///home/ogilo/Projects/church/current/AGENTS.md)
2. **Architecture & Standards**: [`docs/knowledge/`](file:///home/ogilo/Projects/church/current/docs/knowledge/)
3. **Implementation Playbooks**: [`.agents/skills/`](file:///home/ogilo/Projects/church/current/.agents/skills/)

## Quick Reference Commands
- Verification (Lint + Types): `composer check`
- Backend Linting (Pint): `composer pint`
- Frontend Type Check: `npm run type-check`
- Tests: `php artisan test` or `./vendor/bin/pest`
- Version Control: Always commit changes upon task completion using Conventional Commits (`feat:`, `fix:`, `refactor:`, `chore:`).
