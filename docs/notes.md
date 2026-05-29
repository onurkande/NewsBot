# Notes

- The CRUD bundle currently uses the built-in `auth` + `admin` middleware on routes.
- If you want to enforce policy-based authorization later, register a policy for `SourceCategory`.
- The selection state for bulk delete is stored in `sessionStorage` per table key.


# Source Category CRUD refactor

This bundle refactors the existing `source-categories` CRUD into a reusable Laravel architecture:

- Controllers: thin and readable
- FormRequests: validation moved out of controllers
- Service: create / update / delete / bulk delete
- Query: listing, filtering, sorting, pagination
- Blade components: reusable admin UI parts
- JS: persistent row selection, confirm modal, auto-dismissing flash alerts

> The current codebase only included the source-categories CRUD, so this package fully refactors that CRUD and provides the exact structure to replicate for `source-accounts`, `raw-tweets`, and `story-clusters`.

## What to do next

1. Copy the files into your Laravel project.
2. Replace your existing `SourceCategoryController`, model, routes, view files and `app.js`.
3. Run the migration to add soft deletes.
4. Reuse the same folder layout for every future CRUD.

