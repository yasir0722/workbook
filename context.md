# Project Context: Personal Facebook Page Reader

## Project concept

Build a personal web application inspired by TweetDeck, but for Facebook Pages.

This is not an attempt to recreate Facebook. Its purpose is to reduce Facebook usage by providing a controlled reader that shows posts only from Facebook Pages the user intentionally selects.

The application must avoid the distractions of the Facebook interface:

- Algorithmic feed recommendations
- Reels
- Stories
- Random posts
- Suggested Pages
- Unrelated content
- Endless scrolling

Example Pages:

- PASTI Malaysia
- KPM
- JAKIM
- JPJ
- Local community Pages
- Other intentionally selected Pages

```text
Facebook
    |
    | Facebook Graph API
    v
Laravel Backend
    |
    +-- Fetch selected Page posts
    +-- Normalize Facebook data
    +-- Cache/store posts
    +-- Expose application API
             |
             v
           MySQL
             |
             v
          Vue 3 Frontend
             |
             v
       TweetDeck-style UI
```

## Main goal

> Give me Facebook information without making me browse Facebook.

The application should feel like an information dashboard, not a social-media application.

## Product principles

1. The user chooses exactly which Pages to follow in this application.
2. Do not recreate Facebook's algorithmic feed.
3. Do not show recommended content.
4. Do not show random Facebook content.
5. Avoid infinite scrolling.
6. Keep the UI focused and distraction-free.
7. Prefer chronological posts.
8. Allow the user to open the original Facebook post when necessary.
9. The application should be useful even if opened for only a few minutes.
10. Treat Facebook primarily as a content source, not the application's interface.

## Meta/Facebook API considerations

Use the official Meta Graph API where possible.

Do not assume that:

- Following a Page means a third-party application can access that Page.
- Facebook Login automatically grants access to every Page.
- Every Facebook field is available through the API.
- Arbitrary Page content can always be retrieved.

Explicitly investigate and document Meta's current API requirements before implementing the Facebook integration, including:

- Meta Developer App requirements
- Facebook Login
- Graph API
- Page Public Content Access (PPCA)
- Permissions required to retrieve public Page posts
- Whether posts from Pages the user follows can be retrieved
- Access-token requirements
- Rate limits
- Available post and media fields
- API review requirements
- Development mode versus production/live mode
- Current Meta platform policies

Do not invent API permissions or endpoints. Mark uncertain capabilities as **TO VERIFY** rather than assuming they work.

## Authentication concepts

The initial application is intended primarily for personal use.

Possible authorization flow:

```text
User
  |
  v
Facebook Login
  |
  v
Meta authorization
  |
  v
Application receives appropriate token
  |
  v
Laravel securely handles Facebook API communication
```

Do not assume Facebook Login is necessary for every part of the application. Clearly separate:

1. Authentication to this application.
2. Authentication and authorization with Meta.
3. Access to public Facebook Page content.

## Page management

Maintain the application's own list of selected Pages. Do not initially depend on Facebook's "Pages I follow" list.

Example:

```text
My Pages

[x] PASTI Malaysia
[x] KPM
[x] JAKIM
[x] JPJ
[x] Local Community Page

+ Add Page
```

The user should be able to:

- Add a Page
- Remove a Page
- Enable or disable a Page
- Assign a category
- Reorder Pages
- Optionally rename the local display name
- Refresh Page information

Suggested categories:

- Education
- Government
- Religion
- Community
- News
- Technology
- Other

## Feed UI concept

The main interface should be conceptually TweetDeck-like.

```text
+-------------------------------------------------------------+
| My Facebook Reader                              Refresh     |
+------------------+------------------+-----------------------+
| PASTI Malaysia   | KPM              | JAKIM                 |
|                  |                  |                       |
| 10:32 AM         | 10:20 AM         | 9:55 AM               |
| Announcement...  | Circular...      | Program...            |
|                  |                  |                       |
| [image]          | [image]          | [image]               |
|                  |                  |                       |
| Open Facebook -> | Open Facebook -> | Open Facebook ->      |
+------------------+------------------+-----------------------+
| JPJ              | Community        | Technology            |
|                  |                  |                       |
| New update...    | New announcement | New post...           |
+------------------+------------------+-----------------------+
```

Potential future features, which must not be implemented unless explicitly requested:

- Column-based layout
- Drag-and-drop columns
- Compact/list mode
- Chronological global feed
- Unread posts
- Bookmarks
- Search
- Filters
- Categories
- Dark mode
- PWA
- Mobile/tablet layout

## Recommended architecture

### Backend

- Laravel
- MySQL
- Laravel Scheduler
- Laravel Jobs/Queue where useful
- Facebook Graph API

### Frontend

- Vue 3
- Vite
- TypeScript
- REST API communication with Laravel

```text
Facebook Graph API
        |
        v
Facebook service/client
        |
        v
Laravel scheduled job
        |
        v
Post normalization
        |
        v
MySQL
        |
        v
Laravel REST API
        |
        v
Vue 3
        |
        v
TweetDeck-style dashboard
```

Do not have Vue communicate directly with Facebook where avoidable. Facebook API credentials and tokens must remain server-side.

## Initial database concepts

This is an initial design only; do not blindly implement it.

### `facebook_pages`

Possible fields:

- `id`
- `facebook_page_id`
- `name`
- `username`
- `url`
- `category`
- `enabled`
- `sort_order`
- `last_fetched_at`
- `created_at`
- `updated_at`

### `facebook_posts`

Possible fields:

- `id`
- `facebook_page_id`
- `facebook_post_id`
- `message`
- `permalink`
- `published_at`
- Raw or normalized metadata where appropriate
- `created_at`
- `updated_at`

### `facebook_post_media`

Possible fields:

- `id`
- `facebook_post_id`
- `media_type`
- `media_url`
- `thumbnail_url`
- `metadata`

Only store fields that are legally and technically appropriate under Meta API terms. Avoid unnecessary duplication of Facebook content.

## Fetching strategy

The backend should periodically check enabled Pages for new posts.

```text
Laravel Scheduler
       |
       v
Fetch enabled Pages
       |
       v
Facebook Graph API
       |
       v
Normalize response
       |
       v
Check existing facebook_post_id
       |
       v
Insert new posts
       |
       v
Update last_fetched_at
```

The frontend should primarily read from the application's backend and database. Do not make the frontend repeatedly query Facebook.

## Caching and offline resilience

Retain previously retrieved posts so the dashboard can display recently fetched content if Facebook is temporarily unavailable.

Do not design excessive long-term storage until Meta's current API and data-retention policies are verified.

## Security

- Never expose sensitive access tokens to the browser.
- Store secrets in `.env`.
- Do not commit `.env`.
- Use Laravel's server-side API integration.
- Validate Facebook API responses.
- Avoid storing unnecessary user or Facebook data.
- Follow Meta's current platform policies and data-retention requirements.

## MVP scope

The first version should be deliberately small.

### Include

1. Application authentication, if required
2. Meta/Facebook integration proof of concept
3. Add selected Facebook Page
4. Fetch Page information
5. Fetch available Page posts
6. Store permitted data
7. Display posts
8. Multiple Page columns
9. Enable/disable Pages
10. Open original Facebook post
11. Manual refresh
12. Scheduled fetching

### Explicitly exclude

- Facebook personal feed
- Reels
- Stories
- Messenger
- Facebook comments
- Facebook reactions
- Facebook groups
- Facebook Marketplace
- Posting to Facebook
- Liking posts
- Commenting
- Notifications
- Recommendation algorithms
- Scraping
- Browser automation
- Multi-user SaaS
- Monetization

## Scraping

Do not implement Facebook scraping as the default solution. The official Meta API is the preferred architecture.

If the official API cannot support the desired functionality, document the limitation and evaluate alternatives separately. Do not silently switch to scraping.

## Development philosophy

This is primarily a personal productivity and wellbeing tool. Optimize for:

- Simplicity
- Reliability
- Low hosting cost
- Low maintenance
- Minimal distractions
- Clear architecture
- Security
- API compliance

Avoid overengineering. Do not introduce microservices, Kubernetes, complicated event-driven architecture, unnecessary Redis usage, or unnecessary Docker complexity without a clear future requirement.

## Hosting concept

The application is expected to run on a low-cost VPS.

```text
VPS
|
+-- Nginx
+-- Laravel
+-- PHP-FPM
+-- MySQL
+-- Vue frontend
```

Keep infrastructure simple.

## Development workflow

The developer uses VS Code and Git. This file is the project source of truth.

Before making significant architectural changes:

1. Read `context.md`.
2. Check the existing implementation.
3. Preserve existing decisions unless there is a clear reason to change them.
4. Update this file when architecture or product decisions change.

## Instructions for future AI coding sessions

- Read `context.md` first.
- Do not assume Meta API capabilities.
- Verify API requirements before implementing Facebook integration.
- Do not implement scraping unless explicitly approved.
- Do not expand the MVP unnecessarily.
- Prefer a simple Laravel and Vue architecture.
- Keep Facebook credentials and tokens server-side.
- Explain architectural trade-offs before making major changes.
- If Meta documentation is required, use current official Meta documentation rather than old tutorials or blog posts.

## Current project status

This is a new project. No implementation should be assumed to exist.

### Immediate next step

Validate whether the official Meta Graph API can support the exact requirement of retrieving posts from selected public Facebook Pages for this personal reader application.

Only implement Facebook integration after that validation.

## Decision log

Maintain important decisions in this section. Update a decision here when it changes rather than allowing conflicting assumptions to accumulate.

| Decision | Current choice |
| --- | --- |
| Project type | Personal Facebook Page reader |
| UI concept | TweetDeck-inspired multi-column dashboard |
| Backend | Laravel |
| Frontend | Vue 3 + Vite + TypeScript |
| Database | MySQL |
| Facebook integration | Official Meta Graph API preferred |
| Scraping | Not the default approach |
| Hosting | Low-cost VPS |
| Primary goal | Reduce Facebook browsing and distraction |
| MVP | Selected Pages -> posts -> focused dashboard |
