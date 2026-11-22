# Vanguard eClaim API

This API lets another application run the eClaim claim flows: a **customer** files and follows a claim, and **staff** process it.

**Base URL (sandbox):** `https://eclaim.nissitechnologies.com/api/v1`

All requests and responses are JSON. Always send `Accept: application/json`. Send `Content-Type: application/json`, except for file uploads, which use `multipart/form-data`.

## Sandbox

The test environment is a sandbox:

- **No SMS or email is sent.** Messages are written to the server log instead.
- **Customer login uses a fixed one-time code** that is shared with you separately. It only works for phone numbers on a sandbox allowlist.
- Data on this server is test data. Do not rely on it staying.

## Authentication

Every endpoint except the login endpoints needs a token:

```
Authorization: Bearer <access_token>
```

Customer tokens last **60 minutes** and staff tokens **8 hours**. After that, log in again. Tokens are issued for one role only: a customer token is refused on `/staff/*` routes and a staff token is refused on customer routes (HTTP 403, code `forbidden`).

### Customer login (one-time code)

1. **Request a code.** `POST /auth/customer/request-otp`

   ```json
   { "identifier": "0241234567", "login_type": "mobile_no" }
   ```

   `login_type` is optional: `mobile_no` (default), `policy_number` or `vehicle_number`. The response contains a `challenge_id`, valid for 10 minutes, and a masked phone number.

2. **Verify the code.** `POST /auth/customer/verify-otp`

   ```json
   { "challenge_id": "<from step 1>", "otp": "123456" }
   ```

   - One matching customer profile: the response contains `access_token`.
   - Several profiles share the phone number: the response has `status: "profile_selection"` and a list of `profiles` (`id`, `name`). Continue with step 3.

3. **Choose a profile (only when asked).** `POST /auth/customer/select-profile`

   ```json
   { "challenge_id": "<from step 2>", "profile_id": 0 }
   ```

After login, the customer's policies are loaded from Genova and GLIMS in the background. `GET /policies` returns `sync.synced: true` once that finishes. If it is `false`, wait a few seconds and call again.

### Staff login

`POST /auth/staff/login` with `{ "email": "...", "password": "..." }` returns an `access_token`.

### Other

- `GET /auth/me` returns the profile of the token's owner.
- `POST /auth/logout` revokes the current token.

## Customer flow

1. Log in (above).
2. `GET /policies`: the customer's policies. `can_claim` says whether a claim form exists for the policy class. Expired policies are still listed (use `include_expired=1`), because a claim for an event inside the policy period is allowed.
3. `GET /claim-forms/{type}`: the fields for `motor`, `fire` or `general_accident` (see **Claim forms** below).
4. `POST /claims`: file the claim.
5. `POST /claims/{id}/documents`: add supporting documents (jpg, jpeg, png, gif, pdf, xls, xlsx, csv; up to 5 MB each, up to 10 per request).
6. `GET /claims/{id}`: status, timeline and documents.
7. If `action_required` is `true`, the claims team needs more information. Read `pending_info_request.note`, upload documents, then call `POST /claims/{id}/respond-info`.
8. A customer can cancel a claim only while its status is `submitted` (`POST /claims/{id}/cancel`).

### Rules enforced when filing a claim

- `claim_type` must match the policy's class (`MOTOR` is `motor`, `FIRE` is `fire`, `GENERAL ACCIDENT` is `general_accident`).
- The **event date** (see each form's `event_field`) must fall inside the policy period, and not in the future. It must also be within **90 days** of today.
- **Fleet motor policies** need `risk_id`: the **registration number** of the vehicle, taken from `risks[].id` in the policy response.
- Fields in `server_fields` are filled from the policy. They are ignored if you send them.
- Unknown `form_data` fields are rejected with `unknown_fields`.
- The declaration must be accepted (`declaration_agreement: true`) and a `digital_signature` given.

### Safe retries

Send an `Idempotency-Key` header (8 to 100 characters) on `POST /claims`. If the same key is sent again within 24 hours, the original claim is returned with HTTP 200 and the header `Idempotent-Replay: true`, instead of creating a second claim.

### Value formats

Claim values are stored as text, exactly as the web forms store them. In responses, numbers such as `age` come back as strings (`"35"`), booleans as `"true"` / `"false"`, and list fields (injured persons, vehicles, property items) as arrays of objects.

## Staff flow

1. `POST /auth/staff/login`.
2. `GET /staff/claims`: `scope=incoming` (default) or `scope=mine`. Filters: `search` (customer name, policy number or claim number), `status`, `branch`, `updated_since`.
3. `GET /staff/claims/{id}`: full detail. `permissions` says what you can do right now.
4. `POST /staff/claims/{id}/process` takes an unassigned claim (assigns it to you and moves it to `under_review`).
5. Work on it:
   - `PUT /staff/claims/{id}/form-data`: change fields. Send only the fields to change.
   - `POST /staff/claims/{id}/documents`: add documents.
   - `POST /staff/claims/{id}/request-info`: ask the customer for more information (note up to 480 characters). The customer is notified by SMS and the claim moves to `pending_info`.
   - `POST /staff/claims/{id}/send-to-survey` or `/send-to-committee`.
6. `POST /staff/claims/{id}/finalize` closes an **approved or rejected** claim.
7. `POST /staff/claims/{id}/cancel` (a reason is required) and `POST /staff/claims/{id}/reopen` (admin only).

### Who can do what

- Staff roles that can log in: admin, claim head, claims adjuster, staff, surveyor, reviewer.
- Roles that can **change** claims: admin, claim head, claims adjuster and staff. Surveyors and reviewers can read only.
- Only the assigned handler, a claim head or an admin can work on a claim.
- Admins and claim heads can assign a claim to anyone. Everyone else can only take an unassigned claim for themselves.

### Not available in v1

These steps happen in the eClaim web portal only: the surveyor's work (accepting a survey, completing it), the claims committee decision, and the liability guide.

## Errors

Every error has the same shape:

```json
{ "error": { "code": "event_outside_cover", "message": "The event date is before this policy started (30 May 2026)." } }
```

Validation errors (HTTP 422, code `validation_failed`) also contain `details`, keyed by field.

| HTTP | Code | Meaning |
|---|---|---|
| 401 | `unauthenticated` | Missing, invalid or expired token |
| 401 | `invalid_credentials` | Wrong staff email or password |
| 403 | `forbidden` | The token's role cannot use this endpoint |
| 403 | `not_staff`, `forbidden_role`, `not_handler`, `cannot_assign_others`, `account_inactive` | Staff permission problems |
| 404 | `not_found` | Does not exist, or belongs to someone else |
| 409 | `claim_closed`, `not_cancellable`, `no_open_request`, `request_already_open`, `already_assigned`, `not_assignable`, `invalid_status`, `not_decided`, `not_cancelled`, `claim_not_actionable`, `request_in_progress` | The claim is not in a state that allows this |
| 422 | `validation_failed` | Invalid input, with `details` |
| 422 | `unknown_fields`, `claim_type_mismatch`, `event_outside_cover`, `risk_required`, `invalid_risk`, `invalid_idempotency_key`, `invalid_assignee`, `no_form` | A business rule was not met (the message says which) |
| 422 | `identity_not_found`, `invalid_otp`, `challenge_expired`, `invalid_profile`, `otp_required` | Customer login problems |
| 429 | `too_many_requests`, `too_many_attempts` | Rate limit reached |
| 500 | `server_error` | Something went wrong on our side |

## Rate limits

Per token (per IP before login): 60 requests per minute in general, 20 per minute for write requests, and 30 per minute per IP for the login endpoints. A one-time code can be tried 5 times, and 5 codes can be requested per phone number every 10 minutes.

## Documents

Document `url` values are API endpoints: call them with your bearer token. They return the file inline, or as a download with `?download=1`.

## Dates and times

Dates are `YYYY-MM-DD`. Timestamps are ISO 8601 in UTC.