# Page Architecture

## Public Pages

- `/` - Home
- `/about` - About Algonquian Real Estate
- `/contact` - Contact / inquiry entry
- `/sell-your-property` - Public seller intake form

## System Pages

- `/dashboard` - Internal command center
- `/plugins` - Plugin catalog
- `/deals` - Deal management
- `/buyers` - Buyer portal
- `/lenders` - Capital sources
- `/documents` - Document library

## Plugin Routes

Each plugin uses:

- `/plugin/{slug}`
- `/plugin/{slug}/start`
- `/plugin/{slug}/docs`
- `/plugin/{slug}/settings` where applicable

## Deal Flow Routes

- `/deals`
- `/deals/new`
- `/deals/{id}`
- `/deals/{id}/edit`
- `/deals/{id}/documents`
- `/deals/{id}/buyers`

## Pipeline Routes

- `/pipeline`
- `/pipeline/board`
- `/pipeline/activity`

## Buyer Routes

- `/buyers/register`
- `/buyers/login`
- `/buyer-dashboard`
- `/buyer/deals`
- `/buyer/deals/{id}`

## Funding Routes

- `/lenders`
- `/lenders/new`
- `/lenders/{id}`
- `/funding`
- `/funding/{deal-id}`

## Document Routes

- `/documents`
- `/documents/entity`
- `/documents/lender`
- `/documents/acquisition`
- `/documents/financial`
- `/documents/risk`
- `/documents/property-management`
- `/documents/generate`
- `/documents/{deal-id}`
- `/documents/{deal-id}/download`
- `/documents/signatures`
- `/documents/archive`

## Admin Routes

- `/admin/dashboard`
- `/admin/document-control`
- `/admin/system-settings`
- `/admin/users`
- `/admin/logs`
- `/admin/automation`
