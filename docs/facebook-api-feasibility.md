# Facebook API Feasibility

**Status:** Validated against current official Meta documentation on 2026-10-05.  
**Scope:** Technical feasibility only. No Facebook integration has been implemented.

## Executive Summary

**YES, WITH LIMITATIONS.**

Meta's official API has a feature intended for this use case: **Page Public Content
Access (PPCA)**. PPCA allows an approved app to read public posts, public comments,
and business metadata for Pages that the app user does not manage. Its allowed use
includes analyzing and displaying Page posts and engagement.

However, the selected public Pages cannot be read in production until Meta approves
PPCA through App Review. A Facebook follow relationship does not provide API access.
Facebook Login does not grant access to arbitrary Pages. The standard Pages API
token flow only covers Pages where the app user owns the Page or can perform a Page
task.

**Do not implement the Facebook integration until PPCA App Review access has been
approved and the approved access is proven in the Graph API Explorer against a
representative public Page.**

## Exact Use Case

The application is a personal, focused Facebook Page reader:

- One person maintains a manually selected list of public Facebook Pages.
- The person does not necessarily own, administer, or manage those Pages.
- The application periodically reads available public posts from 10-30 selected
  Pages and presents them in a distraction-free, chronological dashboard.
- The application is read-only. It does not access the user's personal feed,
  comments, reactions, Messenger, groups, Stories, Reels, Marketplace, or perform
  posting or other engagement actions.
- Laravel would fetch data server-side and cache the minimum permitted data in
  MySQL for the Vue dashboard.

Examples include PASTI Malaysia, KPM, JAKIM, JPJ, local community Pages, and other
public Pages intentionally selected by the user.

## API Access Model

### Two distinct access paths

| Scenario | Official access model | Applicability to this project |
| --- | --- | --- |
| A Page is owned or task-managed by the app user | Facebook Login for Business -> user access token -> Page access token -> Pages API | Supported, but not the primary use case. |
| A public Page is not managed by the app user | Approved PPCA feature -> Graph API access to public Page data | The required path for the primary use case. |

The [Pages API overview](https://developers.facebook.com/documentation/pages-api/overview)
states that its normal Page-token flow requires the app user to own the Page or be
able to perform a task on it. It also states that a feature can provide access to
public Page data without a permission or Page task.

The [Features Reference](https://developers.facebook.com/docs/features-reference#page-public-content-access)
states that PPCA permits an app to read public business metadata, comments, and
posts for Pages where the app lacks `pages_read_engagement` and
`pages_read_user_content`, and permits analyzing and/or displaying Page posts and
engagement.

### Page access matrix

| Page relationship | Can the app retrieve Page posts? | Conditions |
| --- | --- | --- |
| User owns or can perform a task on the Page | **Yes** | Use the standard Pages API token flow and the Page-specific access token. |
| User follows the Page | **No, not because of following** | Meta documentation does not identify following a Page as an authorization grant or as a source of a readable Page list. |
| Page is public but not managed by the user | **Yes, with limitations** | PPCA must be approved for the live app; endpoint/token/field access must be demonstrated in the approved app. |
| Page is public and is not managed by the user, before PPCA approval | **No in production** | Meta says a live app cannot see public Page content without PPCA. |
| Page is administered by a person who is also an app admin/developer/tester | **Limited development testing only** | The PPCA feature page allows this pre-review testing condition. It does not validate access to unrelated target Pages. |
| Page is private, unpublished, geographically/age restricted, or otherwise unavailable | **TO VERIFY / treat as unavailable** | Public-content access does not override Page-level visibility or eligibility restrictions. |

## Required Meta Products / Permissions

### Required for the unmanaged-public-Page use case

| Requirement | Status | Why it is needed |
| --- | --- | --- |
| Meta developer app | Required | Every Graph API integration requires an app identity. |
| Graph API | Required | Supplies Page search, Page post, and attachment endpoints. |
| Page Public Content Access feature | Required | Official feature for reading public posts, comments, and business metadata from Pages not managed by the app user. |
| Meta App Review for PPCA / Advanced Access | Required for live arbitrary public Pages | Unapproved features are not available for this use in a live app. |
| Business Verification | Required for Advanced Access | Meta's current permission/access documentation says Business Verification is required for apps requesting Advanced Access. |
| Data Use Checkup and data-handling disclosure | Required for a live app with advanced feature access | Meta requires annual Data Use Checkup for live apps with advanced permissions or features. |

### Not required solely to read arbitrary public Page content

| Item | Conclusion |
| --- | --- |
| `pages_read_engagement` | Not the PPCA mechanism. PPCA is explicitly for Pages for which the app lacks this permission. It is relevant to managed-Page operations. |
| `pages_read_user_content` | Not the PPCA mechanism. PPCA is explicitly for Pages for which the app lacks this permission. |
| `pages_show_list` | Not needed to obtain an arbitrary Page list through PPCA. It is used in the managed-Page flow. |
| A Page access token for every selected public Page | Not available for Pages the user does not manage. Page tokens are per Page, user/admin, and app. |
| Facebook Login | Not established as required for PPCA itself. It is required when the application needs a user access token for the managed-Page flow or logged-in Page search. |

### Authentication is three separate concerns

1. **Application authentication:** Laravel authentication for access to this personal
   dashboard. This can be local application authentication and does not require
   Meta.
2. **Meta authorization:** Facebook Login or Facebook Login for Business is used
   when the app needs a user access token, for example to obtain a managed Page's
   Page token.
3. **Public Page access:** PPCA is an app feature grant, not a permission an end
   user can grant during Facebook Login. The [Features Reference](https://developers.facebook.com/docs/features-reference)
   says app users cannot grant features to an app.

## Page Public Content Access

PPCA is the central feasibility gate.

### What PPCA provides

The official [Features Reference](https://developers.facebook.com/docs/features-reference#page-public-content-access)
defines PPCA as access to:

- Pages Search API
- Public business metadata
- Public comments
- Public posts

for Pages for which the app does not have `pages_read_engagement` or
`pages_read_user_content`.

Its documented allowed use is to analyze and/or display posts and engagement on
Pages, which matches this reader's read-only dashboard purpose.

### App Review and live-mode constraints

The official [PPCA feature page](https://developers.facebook.com/docs/features-reference/page-public-content-access/)
states:

- Before review, testing is limited to content on a Page where a Page admin also
  has an administrator, developer, or tester role on the app.
- To access public content on other Pages, the feature must be submitted for
  review.
- Once the app is in live mode, it cannot see public Page content without this
  feature.

The general [App Review documentation](https://developers.facebook.com/docs/apps/review/)
also states that unapproved features are limited to app-role users, and that review
must allow Meta to test the requested functionality.

### Availability to a personal developer

PPCA is documented as a Meta app feature, not as a privilege granted merely by
following Pages. A personal use case does not bypass the live-mode review
requirement. The practical approval path is:

1. Create a Meta app.
2. Request PPCA Advanced Access and submit the reader's exact read-only use case to
   App Review.
3. Complete Business Verification if required by the Advanced Access request.
4. Complete the required data-handling information and Data Use Checkup.
5. After approval, make a proof-of-concept request against a public Page not
   managed by the account.

Meta can approve, narrow, reject, or later change this access. Approval is therefore
a project dependency, not an implementation detail.

## API Endpoints

Only endpoints evidenced by current official Meta documentation are listed here.
Use the Graph API version accepted by the app at implementation time; Meta's current
documentation contains examples using both `v25.0` and `v26.0`, so the exact
production version must be pinned and rechecked immediately before the proof of
concept.

| Endpoint | Verified purpose | Access notes |
| --- | --- | --- |
| `GET /pages/search?q={query}&fields=id,name,location,link` | Search public Pages and obtain Page IDs. | The [Page Search guide](https://developers.facebook.com/documentation/pages-api/search-pages) explicitly supports an app access token with PPCA when the app user is not logged in and is searching Pages for competitive analysis. |
| `GET /{page-id}/feed` | Retrieve Page feed posts. | The [Pages API Get Started guide](https://developers.facebook.com/documentation/pages-api/getting-started) verifies this read endpoint and its `id`, `message`, and `created_time` response fields for a managed Page. PPCA's exact token and field behavior for an unmanaged Page must be demonstrated after approval. |
| `GET /{page-post-id}/attachments` | Retrieve attachments for a Page post. | The [Page Post Attachments reference](https://developers.facebook.com/docs/graph-api/reference/page-post/attachments/) documents this edge and its default limit of 12 attachments. It uses a `v26.0` example. |
| `GET /{page-id}?fields={fields}` | Retrieve selected Page metadata. | The [Manage a Page guide](https://developers.facebook.com/documentation/pages-api/manage-pages) documents requesting Page fields and identifies PPCA for Page ID discovery. Request only fields approved and needed by the reader. |

### Explicit non-assumption

`GET /{page-id}/posts` is **not** listed as a verified endpoint in this document.
Do not implement it from old tutorials. The current official Pages guide verifies
`GET /{page-id}/feed`; use that endpoint for the proof of concept and confirm the
approved PPCA response shape before writing integration code.

## Available Data

The table distinguishes data verified by current documentation from data that may be
available only after PPCA approval and a representative API response. No fields
should be persisted or displayed until the proof of concept confirms them.

| Data | Available? | Notes |
| --- | --- | --- |
| Post ID | **Yes** | `id` is shown in the official `GET /{page-id}/feed` response. |
| Message | **Yes** | `message` is shown in the official `GET /{page-id}/feed` response. A post can have no message. |
| Created time | **Yes** | `created_time` is shown in the official `GET /{page-id}/feed` response. |
| Permalink | **TO VERIFY** | Do not assume `permalink_url` is returned or approved for PPCA. Confirm the requested Page Post field in the approved app. The fallback UI action must not be designed until this is confirmed. |
| Image | **Conditional** | The documented `/{page-post-id}/attachments` edge can return attachments, but exact image fields, URLs, durability, and PPCA response scope must be proven. |
| Video | **Conditional** | Attachments can represent post media, but video fields, playback URLs, and restrictions must be proven. Do not plan to download, mirror, or host videos. |
| Attachments | **Yes, conditional on authorized post access** | The attachment edge is documented; it returns at most 12 by default unless a larger subattachment limit is requested. |
| Link preview | **TO VERIFY** | Do not assume `link`, `picture`, title, description, or preview media fields are available. Confirm approved fields in the API response. |
| Comments | **Conditional** | PPCA's feature description includes public comments, but comments are outside the MVP and must not be fetched or stored without a separate approved need. |
| Reactions | **TO VERIFY / out of scope** | PPCA permits displaying "engagement," but the retrieved current documentation does not establish a reaction field or edge for this reader. Do not request it. |

### Minimal initial response fields

If PPCA approval and proof of concept succeed, begin only with:

```text
id
message
created_time
```

Add an original-post URL, attachment metadata, or thumbnails only after each field
has been verified in the live approved app and has a documented storage purpose.

## Rate Limits

Meta does not publish a fixed universal request number for this use case.

The current [Rate Limiting documentation](https://developers.facebook.com/docs/graph-api/overview/rate-limiting/)
states that:

- All Pages endpoint requests are rate limited.
- Pages API requests made with Page or system-user tokens use Business Use Case
  rate-limit logic.
- Requests made with app or user access tokens are subject to application or user
  rate limits.
- The applicable quota depends on the endpoint and token type.
- Usage must be monitored through Graph API rate-limit response headers and the Meta
  App Dashboard.

For this application, polling 10-30 Pages every 5-15 minutes would be approximately
40-360 Page-feed requests per hour before pagination, retries, Page search, or
attachment requests. That is a small workload in general web-application terms, but
Meta's quota is dynamic and application-dependent. It must not be treated as
guaranteed safe.

### Required operational controls

- Fetch only enabled Pages.
- Fetch only the newest page of posts and paginate only when required.
- Use a per-Page schedule with jitter rather than a synchronized burst.
- Persist the newest successfully fetched post timestamp/ID per Page.
- Record Graph API rate-limit headers and response errors.
- Back off on throttling errors, including documented Page error `80001`.
- Do not refetch attachment data unless the dashboard needs it.

The proof of concept must measure actual app quota use and response headers before
selecting the scheduler interval.

## Token Lifecycle

### Managed-Page flow

For a Page the user manages, Meta documents this path:

```text
Facebook Login for Business
  -> user access token
  -> GET /{user-id}/accounts
  -> Page access token for a Page where the user has a role/task
  -> Pages API calls
```

The [Access Tokens guide](https://developers.facebook.com/docs/facebook-login/access-tokens/)
states:

- User tokens obtained through web login are normally short-lived (typically one to
  two hours).
- A user token can generally be exchanged server-side for a long-lived token
  (typically about 60 days).
- A long-lived Page token can be obtained from a long-lived user token only when
  the user has a Page role.
- Page tokens are unique to the Page, administrator, and app.
- Long-lived Page tokens do not have a fixed expiration date but can still be
  invalidated.
- If a permission is unused for 90 days, Meta says the app user normally must grant
  it again.

### Public unmanaged-Page PPCA flow

PPCA is an app feature, not a user-granted permission. The [Page Search guide](https://developers.facebook.com/documentation/pages-api/search-pages)
explicitly permits an **app access token with PPCA** for unauthenticated Page
searches. The retrieved current documentation does not explicitly state which token
type the `/{page-id}/feed` request accepts for PPCA public-post reads.

Therefore:

- **Token type for PPCA Page-feed reads: TO VERIFY in the approved Graph API
  Explorer proof of concept.**
- Do not assume a Page token can be obtained for an unmanaged Page.
- Do not make Facebook Login a product requirement unless the approved endpoint flow
  demonstrably requires a user token.

### Token security

All Graph API calls belong in Laravel. Access tokens and the Meta app secret must:

- Stay in server-side secrets management such as `.env`.
- Never be returned by the Laravel API.
- Never be exposed to Vue, browser storage, client-side code, logs, or URLs.
- Be validated and monitored for expiry/invalidation.

Meta's [Login Security guide](https://developers.facebook.com/docs/facebook-login/security/)
requires HTTPS for apps, says never to include the app secret in client-side code,
and recommends secure server-to-server calls.

## Data Storage / Retention

### What the current documentation supports

PPCA's documented allowed usage includes displaying public Page posts and
engagement. Caching the minimum approved response data in MySQL to render this
reader can be compatible with that purpose **only if it complies with the final
approved feature scope and Meta Platform Terms**.

### What is not established

The official material reviewed for this validation does **not** provide a universal
numeric retention period for public Page post text, Page/post IDs, timestamps, media
URLs, thumbnails, or derived metadata. Therefore, this document does not claim that
any item may be stored indefinitely.

The retention period and permitted fields must be confirmed during App Review and
Data Use Checkup. If Meta's approval, dashboard requirements, or endpoint reference
sets stricter conditions, those conditions control.

### Conservative cache policy for a later approved implementation

Until the exact terms are confirmed, use this policy as the design ceiling:

| Data | Storage recommendation |
| --- | --- |
| Page ID, local selection settings, fetch state | Store only as required to fetch selected Pages. |
| Post ID, message, created time | Cache only if returned under approved PPCA access and needed to display the recent reader feed. |
| Permalink | Store only after the field is verified and needed for the "Open Facebook" action. |
| Media URLs and thumbnails | Treat as volatile references; do not copy, proxy, or rehost media unless Meta explicitly permits it. |
| Raw API payloads | Do not store by default. Keep only a short, redacted diagnostic record if required for error investigation and allowed by policy. |
| Comments, reactions, user data | Do not fetch or store; they are outside MVP. |

The future implementation must include documented deletion and cache-expiry behavior,
follow Meta's current Platform Terms and Developer Policies, and remove data if
Meta's authorization or policy requires it.

## Production Requirements

### Development to production

| Stage | Verified requirement |
| --- | --- |
| Development | App-role users can test features, but PPCA pre-review testing is limited to Pages jointly administered by a Page admin and an app-role user. |
| App Review | Submit PPCA with the exact reader purpose and test instructions. Meta must be able to test the functionality. |
| Advanced Access | Required for feature access beyond app-role users; Meta's access-level docs require App Review for Advanced Access. |
| Business Verification | Required when requesting Advanced Access, according to Meta's Business Verification documentation. |
| Data handling | Complete Meta data-handling questions when required by the advanced feature access. |
| Data Use Checkup | Required annually for a live app with Advanced Access to a feature; must be completed before switching the app to live mode when applicable. |
| Live mode | A live app cannot access arbitrary public Page content without PPCA approval. |

### Facebook Login requirements

Facebook Login is required only if the chosen approved flow needs a user access token,
such as the standard managed-Page flow. If used, Meta's current security
documentation requires:

- HTTPS.
- Exact configured **Valid OAuth Redirect URIs**.
- Strict redirect URI matching.
- Secure handling of the app secret and tokens.

### Privacy policy and terms

The exact current App Dashboard metadata and review-submission requirements must be
verified in the Meta dashboard before submitting PPCA. Provide a public privacy
policy and terms of service if Meta requires them for the selected app type, Login
configuration, or review submission. This is **TO VERIFY in the current dashboard**;
it must not be invented or omitted based on old tutorials.

## Risks

1. **App Review denial or scope restriction:** PPCA access is required for the main
   value proposition and Meta may not approve this personal-reader use case.
2. **Feature or policy change:** Meta can change Graph API fields, versions,
   review criteria, and retention requirements.
3. **Token ambiguity for Page-feed reads:** Current retrieved documentation is
   explicit about PPCA app tokens for Page search but not explicit about the token
   accepted by the public Page feed endpoint. Approval-time proof is required.
4. **Incomplete/limited Page data:** Public status does not guarantee every post,
   attachment, media field, URL, or field value is returned.
5. **Rate-limit throttling:** The quota is dynamic, token-dependent, and endpoint-
   dependent. Scheduler cadence needs real response-header measurements.
6. **Media volatility:** Attachment image/video URLs may expire or be restricted;
   this reader must not rely on downloading or rehosting media.
7. **Retention compliance:** No numeric cache duration was verified. Long-term
   archiving must not be assumed.
8. **Personal-use does not eliminate platform requirements:** Production access to
   unrelated public Pages still requires the PPCA approval path.

## Recommended Architecture

Proceed only after PPCA approval and a successful official Graph API proof of
concept. The recommended architecture remains simple and keeps Meta access
server-side:

```text
Vue 3 dashboard
        |
        v
Laravel REST API
        |
        v
MySQL recent-post cache
        ^
        |
Laravel Scheduler / queued fetch job
        ^
        |
Meta Graph API with approved PPCA
```

Architecture constraints:

- Vue never calls Meta directly.
- Laravel owns the approved access token, request validation, rate-limit handling,
  normalization, and cache expiry.
- MySQL stores only the minimum approved data required to render recent posts.
- The scheduler fetches only enabled, manually selected Pages.
- The dashboard uses local cached data, not a browser-to-Meta polling loop.
- No scraping, browser automation, comments, reactions, personal feed, or
  recommendation features.

## MVP Recommendation

**Do not start the Laravel/Vue Facebook integration yet.**

First complete this non-code acceptance gate:

1. Create the Meta developer app.
2. Request and obtain PPCA Advanced Access through App Review.
3. Complete any required Business Verification, data-handling questions, and Data
   Use Checkup.
4. In Graph API Explorer, verify the approved app can:
   - search or otherwise resolve an unrelated selected public Page;
   - read `GET /{page-id}/feed`;
   - retrieve the required minimal fields (`id`, `message`, `created_time`);
   - retrieve a usable original-post URL, if one is required;
   - retrieve authorized attachment data, if images or videos are required;
   - do so using the token type Meta accepts for PPCA Page-feed reads.
5. Record the resulting API version, request, exact response fields, rate-limit
   headers, approval scope, and retention requirements in this document.

If PPCA is denied or the proof of concept cannot read posts from Pages not managed
by the user, stop rather than adding scraping. Evaluate non-Facebook alternatives
separately, such as each organization's website RSS/feed, email newsletter, official
website APIs, or manually curated links.

## Decision

> **Need Meta approval before implementation**

The official Meta API is technically aligned with the intended reader through Page
Public Content Access, but PPCA approval and an approved-app proof of concept are
mandatory before the application can claim support for arbitrary selected public
Facebook Pages.

## Official Sources

- [Pages API Overview](https://developers.facebook.com/documentation/pages-api/overview)
- [Page Public Content Access feature](https://developers.facebook.com/docs/features-reference/page-public-content-access/)
- [Meta Features Reference](https://developers.facebook.com/docs/features-reference#page-public-content-access)
- [Pages API Get Started](https://developers.facebook.com/documentation/pages-api/getting-started)
- [Search for a Page](https://developers.facebook.com/documentation/pages-api/search-pages)
- [Page Post Attachments reference](https://developers.facebook.com/docs/graph-api/reference/page-post/attachments/)
- [Access Tokens](https://developers.facebook.com/docs/facebook-login/access-tokens/)
- [Long-Lived Access Tokens](https://developers.facebook.com/documentation/facebook-login/guides/access-tokens/get-long-lived)
- [Graph API Rate Limiting](https://developers.facebook.com/docs/graph-api/overview/rate-limiting/)
- [App Review](https://developers.facebook.com/docs/apps/review/)
- [Access Levels](https://developers.facebook.com/docs/graph-api/overview/access-levels/)
- [Business Verification](https://developers.facebook.com/docs/apps/business-verification/)
- [Data Use Checkup](https://developers.facebook.com/docs/development/maintaining-data-access/data-use-checkup/)
- [Facebook Login Security](https://developers.facebook.com/docs/facebook-login/security/)
