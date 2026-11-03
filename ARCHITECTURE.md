# Architecture: flexcore-server (WordPress plugin)

The WordPress side of Happydot Rewards: it renders the member-facing pages
(rewards catalogue, reward preview and redemption, profile, histories) and
forwards every read and write to `happydot-flexcore-node`. It stores no
member data of its own; the session token in `$_SESSION` is the only state
that matters.

## Shape

- `flexcore-server.php` is the entry point and loads `includes/`,
  `admin/` and `public/`.
- `includes/class-flexcore-server-api.php` is the only HTTP client for the
  node API. One `request()` method carries the base URL, JSON envelope and
  error mapping; every endpoint is a small method on the same class.
- `includes/class-flexcore-server-ajax-handler.php` exposes those methods
  to the browser as `wp_ajax_` actions, each guarded by a nonce.
- `includes/class-flexcore-server-session.php` holds the member token.
- `public/js/modules/*.js` is the browser code for each page;
  `public/class-flexcore-server-public.php` enqueues it.

A member action moves: page JS → nonce-guarded AJAX action → API method →
node → JSON back to the page.

## Authentication

The member's token lives in the PHP session and is passed to node as
`Authorization: Bearer <token>`. Any endpoint whose answer depends on who
is asking must send it: the rewards catalogue does (so preview rewards
reach the panelists they are listed for) and so does the reward detail
call behind the preview page, because node applies the same visibility
rule to a direct id.

`get_profile()` returns the node profile payload. `name` is the preferred
name first; `fullName` is the name on record (the MyInfo legal name once
Singpass is linked). The redemption prefill reads `fullName`.

## Deploy

- Staging WordPress host and prod WordPress host are separate. Prod ships by
  SCP only, and only after the parity check in the deploy ref.
- Staging is a git checkout and ships by pull.
- The plugin version constant is `FLEXCORE_SERVER_VERSION`; scripts that
  change often are cache-busted with `filemtime()` instead.

## Traps

- `public/js/modules/PostalCode/class-flexcore-server-api.php` is an old copy
  of the API class. Do not edit it expecting behaviour to change.
- A nonce guards each AJAX action; a new action needs both the `wp_ajax_` and
  `wp_ajax_nopriv_` registration to be reachable logged out.
- The preview and redemption pages assume a signed-in member. Code on that
  path may index an API error as an array, so a missing token fails harder
  than it looks.
