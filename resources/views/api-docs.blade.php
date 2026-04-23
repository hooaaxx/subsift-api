<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SubSift API Documentation</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0f1117;
            --surface: #1a1d27;
            --surface-alt: #222636;
            --border: #2e3347;
            --text: #e2e8f0;
            --muted: #8892a4;
            --accent: #6366f1;
            --accent-dim: #3730a3;
            --get: #22c55e;
            --post: #3b82f6;
            --put: #f59e0b;
            --patch: #8b5cf6;
            --delete: #ef4444;
            --get-bg: #052e16;
            --post-bg: #0c1a3b;
            --put-bg: #2d1f04;
            --patch-bg: #1e0b3b;
            --delete-bg: #2d0a0a;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            min-height: 100vh;
        }

        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* Layout */
        .layout { display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 240px;
            flex-shrink: 0;
            background: var(--surface);
            border-right: 1px solid var(--border);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            padding: 24px 0;
        }

        .sidebar-logo {
            padding: 0 20px 20px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 16px;
        }
        .sidebar-logo .name { font-size: 16px; font-weight: 700; color: var(--text); }
        .sidebar-logo .version {
            display: inline-block;
            margin-top: 4px;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 999px;
            background: var(--accent-dim);
            color: #a5b4fc;
            letter-spacing: .3px;
        }

        .nav-group { margin-bottom: 4px; }
        .nav-group-title {
            padding: 6px 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--muted);
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 20px;
            cursor: pointer;
            color: var(--muted);
            transition: color .15s, background .15s;
            font-size: 13px;
            text-decoration: none;
        }
        .nav-item:hover { background: var(--surface-alt); color: var(--text); }
        .nav-item .method-badge { font-size: 9px; font-weight: 700; padding: 1px 5px; border-radius: 3px; min-width: 36px; text-align: center; }

        /* Main content */
        .main { flex: 1; min-width: 0; }

        .hero {
            background: linear-gradient(135deg, #0f1117 0%, #1a1d27 50%, #1e1b4b 100%);
            border-bottom: 1px solid var(--border);
            padding: 48px 48px 40px;
        }
        .hero h1 { font-size: 28px; font-weight: 700; margin-bottom: 8px; }
        .hero p { color: var(--muted); max-width: 560px; line-height: 1.7; }

        .meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 20px;
        }
        .meta-chip {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--muted);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 5px 10px;
        }
        .meta-chip .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--get); }

        .content { padding: 40px 48px; max-width: 900px; }

        /* Sections */
        .section { margin-bottom: 56px; }
        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }
        .section-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: var(--surface-alt);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }
        .section-header h2 { font-size: 18px; font-weight: 600; }
        .section-desc { color: var(--muted); font-size: 13px; margin-top: 2px; }

        /* Endpoint card */
        .endpoint {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            margin-bottom: 12px;
            overflow: hidden;
        }

        .endpoint-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            cursor: pointer;
            user-select: none;
        }
        .endpoint-header:hover { background: var(--surface-alt); }

        .method {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 5px;
            min-width: 52px;
            text-align: center;
            letter-spacing: .3px;
        }
        .method.GET    { color: var(--get);    background: var(--get-bg); }
        .method.POST   { color: var(--post);   background: var(--post-bg); }
        .method.PUT    { color: var(--put);    background: var(--put-bg); }
        .method.PATCH  { color: var(--patch);  background: var(--patch-bg); }
        .method.DELETE { color: var(--delete); background: var(--delete-bg); }

        .path {
            font-family: 'Consolas', 'Fira Code', monospace;
            font-size: 13px;
            color: var(--text);
            flex: 1;
        }
        .path .param { color: #f97316; }

        .endpoint-desc { color: var(--muted); font-size: 13px; margin-left: auto; }

        .auth-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 4px;
            white-space: nowrap;
        }
        .auth-badge.protected { background: #1c1332; color: #a78bfa; border: 1px solid #4c1d95; }
        .auth-badge.public    { background: #052e16; color: #4ade80; border: 1px solid #14532d; }

        /* Endpoint body */
        .endpoint-body {
            display: none;
            border-top: 1px solid var(--border);
            padding: 20px 18px;
            background: var(--surface-alt);
        }
        .endpoint.open .endpoint-body { display: block; }
        .endpoint.open .endpoint-header { background: var(--surface-alt); }

        .body-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 700px) { .body-grid { grid-template-columns: 1fr; } }

        .body-section h4 {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--muted);
            margin-bottom: 10px;
        }

        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { padding: 7px 10px; text-align: left; border-bottom: 1px solid var(--border); }
        th { color: var(--muted); font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; }
        td code { font-family: monospace; color: #f97316; font-size: 12px; }
        .required { color: #f87171; font-size: 10px; font-weight: 600; }
        .optional { color: var(--muted); font-size: 10px; }

        pre {
            background: #0d1117;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 12px 14px;
            overflow-x: auto;
            font-family: 'Consolas', 'Fira Code', monospace;
            font-size: 12px;
            line-height: 1.6;
            color: #e2e8f0;
        }
        .json-key    { color: #7dd3fc; }
        .json-str    { color: #86efac; }
        .json-num    { color: #fbbf24; }
        .json-bool   { color: #f472b6; }
        .json-null   { color: var(--muted); }

        /* Info boxes */
        .info-box {
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            border-left: 3px solid;
        }
        .info-box.blue { background: #0c1a3b; border-color: #3b82f6; color: #93c5fd; }
        .info-box.purple { background: #1c1332; border-color: #8b5cf6; color: #c4b5fd; }

        .base-url-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }
        .base-url-box .label { font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: .4px; white-space: nowrap; }
        .base-url-box code { font-family: monospace; font-size: 13px; color: #a5b4fc; }

        /* Response format */
        .format-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 20px;
        }

        .chevron {
            color: var(--muted);
            font-size: 12px;
            margin-left: auto;
            transition: transform .2s;
        }
        .endpoint.open .chevron { transform: rotate(180deg); }

        .full-width { grid-column: 1 / -1; }

        @media (max-width: 900px) {
            .sidebar { display: none; }
            .hero, .content { padding-left: 24px; padding-right: 24px; }
        }
    </style>
</head>
<body>
<div class="layout">

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-logo">
            <div class="name">SubSift API</div>
            <span class="version">v1</span>
        </div>

        <div class="nav-group">
            <div class="nav-group-title">Overview</div>
            <a href="#overview" class="nav-item">Base URL &amp; Auth</a>
            <a href="#response-format" class="nav-item">Response Format</a>
        </div>

        <div class="nav-group">
            <div class="nav-group-title">Authentication</div>
            <a href="#auth-register" class="nav-item">
                <span class="method-badge" style="background:#0c1a3b;color:#3b82f6;">POST</span> Register
            </a>
            <a href="#auth-login" class="nav-item">
                <span class="method-badge" style="background:#0c1a3b;color:#3b82f6;">POST</span> Login
            </a>
            <a href="#auth-me" class="nav-item">
                <span class="method-badge" style="background:#052e16;color:#22c55e;">GET</span> Me
            </a>
            <a href="#auth-logout" class="nav-item">
                <span class="method-badge" style="background:#0c1a3b;color:#3b82f6;">POST</span> Logout
            </a>
            <a href="#auth-forgot" class="nav-item">
                <span class="method-badge" style="background:#0c1a3b;color:#3b82f6;">POST</span> Forgot Password
            </a>
            <a href="#auth-reset" class="nav-item">
                <span class="method-badge" style="background:#0c1a3b;color:#3b82f6;">POST</span> Reset Password
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-group-title">Subscriptions</div>
            <a href="#subs-list" class="nav-item">
                <span class="method-badge" style="background:#052e16;color:#22c55e;">GET</span> List
            </a>
            <a href="#subs-create" class="nav-item">
                <span class="method-badge" style="background:#0c1a3b;color:#3b82f6;">POST</span> Create
            </a>
            <a href="#subs-show" class="nav-item">
                <span class="method-badge" style="background:#052e16;color:#22c55e;">GET</span> Show
            </a>
            <a href="#subs-update" class="nav-item">
                <span class="method-badge" style="background:#2d1f04;color:#f59e0b;">PUT</span> Update
            </a>
            <a href="#subs-delete" class="nav-item">
                <span class="method-badge" style="background:#2d0a0a;color:#ef4444;">DEL</span> Delete
            </a>
            <a href="#subs-summary" class="nav-item">
                <span class="method-badge" style="background:#052e16;color:#22c55e;">GET</span> Summary
            </a>
            <a href="#subs-upcoming" class="nav-item">
                <span class="method-badge" style="background:#052e16;color:#22c55e;">GET</span> Upcoming
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-group-title">Notifications</div>
            <a href="#notif-list" class="nav-item">
                <span class="method-badge" style="background:#052e16;color:#22c55e;">GET</span> List
            </a>
            <a href="#notif-unread" class="nav-item">
                <span class="method-badge" style="background:#052e16;color:#22c55e;">GET</span> Unread Count
            </a>
            <a href="#notif-read" class="nav-item">
                <span class="method-badge" style="background:#1e0b3b;color:#8b5cf6;">PATCH</span> Mark Read
            </a>
            <a href="#notif-read-all" class="nav-item">
                <span class="method-badge" style="background:#1e0b3b;color:#8b5cf6;">PATCH</span> Read All
            </a>
        </div>
    </nav>

    <!-- Main -->
    <div class="main">
        <div class="hero">
            <h1>SubSift API</h1>
            <p>REST API for managing subscriptions, renewals, and notifications. All endpoints return JSON and are versioned under <code style="color:#a5b4fc">/api/v1/</code>.</p>
            <div class="meta-row">
                <div class="meta-chip"><span class="dot"></span> Online</div>
                <div class="meta-chip">Version 1.0</div>
                <div class="meta-chip">Laravel 13</div>
                <div class="meta-chip">Sanctum Auth</div>
            </div>
        </div>

        <div class="content">

            <!-- Overview -->
            <div class="section" id="overview">
                <div class="section-header">
                    <div class="section-icon">🌐</div>
                    <div>
                        <h2>Overview</h2>
                        <div class="section-desc">Base URL and authentication</div>
                    </div>
                </div>

                <div class="base-url-box">
                    <span class="label">Base URL</span>
                    <code>{{ url('/api/v1') }}</code>
                </div>

                <div class="info-box blue">
                    <strong>Authentication:</strong> Protected endpoints require a Bearer token obtained from
                    <code>/api/v1/auth/login</code> or <code>/api/v1/auth/register</code>.
                    Pass it in the <code>Authorization</code> header:
                    <code>Authorization: Bearer &lt;token&gt;</code>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section" id="response-format">
                <div class="section-header">
                    <div class="section-icon">📦</div>
                    <div>
                        <h2>Response Format</h2>
                        <div class="section-desc">All responses follow a consistent envelope</div>
                    </div>
                </div>

                <div class="format-card">
                    <div class="body-grid">
                        <div class="body-section">
                            <h4>Success</h4>
                            <pre><span class="json-key">"success"</span>: <span class="json-bool">true</span>,
<span class="json-key">"message"</span>: <span class="json-str">"Operation successful"</span>,
<span class="json-key">"data"</span>: { <span class="json-null">/* payload */</span> }</pre>
                        </div>
                        <div class="body-section">
                            <h4>Error</h4>
                            <pre><span class="json-key">"success"</span>: <span class="json-bool">false</span>,
<span class="json-key">"message"</span>: <span class="json-str">"Error description"</span>,
<span class="json-key">"errors"</span>: { <span class="json-null">/* validation errors */</span> }</pre>
                        </div>
                        <div class="body-section full-width">
                            <h4>Paginated List</h4>
                            <pre><span class="json-key">"success"</span>: <span class="json-bool">true</span>,
<span class="json-key">"data"</span>: [ <span class="json-null">/* array of items */</span> ],
<span class="json-key">"meta"</span>: {
  <span class="json-key">"current_page"</span>: <span class="json-num">1</span>,
  <span class="json-key">"last_page"</span>: <span class="json-num">3</span>,
  <span class="json-key">"per_page"</span>: <span class="json-num">15</span>,
  <span class="json-key">"total"</span>: <span class="json-num">42</span>
}</pre>
                        </div>
                    </div>

                    <div style="margin-top:16px">
                        <h4 class="body-section" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--muted);margin-bottom:10px;">HTTP Status Codes</h4>
                        <table>
                            <thead><tr><th>Code</th><th>Meaning</th></tr></thead>
                            <tbody>
                                <tr><td><code>200</code></td><td>OK — request succeeded</td></tr>
                                <tr><td><code>201</code></td><td>Created — resource created</td></tr>
                                <tr><td><code>401</code></td><td>Unauthenticated — missing or invalid token</td></tr>
                                <tr><td><code>403</code></td><td>Forbidden — insufficient permissions</td></tr>
                                <tr><td><code>404</code></td><td>Not Found — resource does not exist</td></tr>
                                <tr><td><code>422</code></td><td>Unprocessable — validation failed</td></tr>
                                <tr><td><code>500</code></td><td>Server Error — unexpected failure</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ─── AUTHENTICATION ─── -->
            <div class="section">
                <div class="section-header">
                    <div class="section-icon">🔐</div>
                    <div>
                        <h2>Authentication</h2>
                        <div class="section-desc">Register, login, and manage user sessions</div>
                    </div>
                </div>

                <!-- Register -->
                <div class="endpoint" id="auth-register">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method POST">POST</span>
                        <span class="path">/api/v1/auth/register</span>
                        <span class="endpoint-desc">Register a new user</span>
                        <span class="auth-badge public">Public</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-grid">
                            <div class="body-section">
                                <h4>Request Body</h4>
                                <table>
                                    <thead><tr><th>Field</th><th>Type</th><th>Required</th></tr></thead>
                                    <tbody>
                                        <tr><td><code>name</code></td><td>string</td><td><span class="required">required</span></td></tr>
                                        <tr><td><code>email</code></td><td>string (email)</td><td><span class="required">required</span></td></tr>
                                        <tr><td><code>password</code></td><td>string (min 8)</td><td><span class="required">required</span></td></tr>
                                        <tr><td><code>password_confirmation</code></td><td>string</td><td><span class="required">required</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="body-section">
                                <h4>Response <span style="color:var(--get);font-size:11px;">201</span></h4>
                                <pre><span class="json-key">"success"</span>: <span class="json-bool">true</span>,
<span class="json-key">"message"</span>: <span class="json-str">"Registered successfully"</span>,
<span class="json-key">"data"</span>: {
  <span class="json-key">"token"</span>: <span class="json-str">"1|abc123..."</span>,
  <span class="json-key">"user"</span>: {
    <span class="json-key">"id"</span>: <span class="json-num">1</span>,
    <span class="json-key">"name"</span>: <span class="json-str">"John"</span>,
    <span class="json-key">"email"</span>: <span class="json-str">"john@example.com"</span>
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Login -->
                <div class="endpoint" id="auth-login">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method POST">POST</span>
                        <span class="path">/api/v1/auth/login</span>
                        <span class="endpoint-desc">Authenticate and get token</span>
                        <span class="auth-badge public">Public</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-grid">
                            <div class="body-section">
                                <h4>Request Body</h4>
                                <table>
                                    <thead><tr><th>Field</th><th>Type</th><th>Required</th></tr></thead>
                                    <tbody>
                                        <tr><td><code>email</code></td><td>string (email)</td><td><span class="required">required</span></td></tr>
                                        <tr><td><code>password</code></td><td>string</td><td><span class="required">required</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="body-section">
                                <h4>Response <span style="color:var(--get);font-size:11px;">200</span></h4>
                                <pre><span class="json-key">"success"</span>: <span class="json-bool">true</span>,
<span class="json-key">"message"</span>: <span class="json-str">"Login successful"</span>,
<span class="json-key">"data"</span>: {
  <span class="json-key">"token"</span>: <span class="json-str">"2|xyz456..."</span>,
  <span class="json-key">"user"</span>: { <span class="json-null">/* user object */</span> }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Me -->
                <div class="endpoint" id="auth-me">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method GET">GET</span>
                        <span class="path">/api/v1/auth/me</span>
                        <span class="endpoint-desc">Get authenticated user</span>
                        <span class="auth-badge protected">Protected</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-grid">
                            <div class="body-section">
                                <h4>Headers</h4>
                                <table>
                                    <thead><tr><th>Header</th><th>Value</th></tr></thead>
                                    <tbody>
                                        <tr><td><code>Authorization</code></td><td>Bearer &lt;token&gt;</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="body-section">
                                <h4>Response <span style="color:var(--get);font-size:11px;">200</span></h4>
                                <pre><span class="json-key">"success"</span>: <span class="json-bool">true</span>,
<span class="json-key">"data"</span>: {
  <span class="json-key">"id"</span>: <span class="json-num">1</span>,
  <span class="json-key">"name"</span>: <span class="json-str">"John Doe"</span>,
  <span class="json-key">"email"</span>: <span class="json-str">"john@example.com"</span>,
  <span class="json-key">"created_at"</span>: <span class="json-str">"2025-01-01T00:00:00Z"</span>
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logout -->
                <div class="endpoint" id="auth-logout">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method POST">POST</span>
                        <span class="path">/api/v1/auth/logout</span>
                        <span class="endpoint-desc">Revoke current token</span>
                        <span class="auth-badge protected">Protected</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-section">
                            <h4>Response <span style="color:var(--get);font-size:11px;">200</span></h4>
                            <pre><span class="json-key">"success"</span>: <span class="json-bool">true</span>,
<span class="json-key">"message"</span>: <span class="json-str">"Logged out successfully"</span></pre>
                        </div>
                    </div>
                </div>

                <!-- Forgot Password -->
                <div class="endpoint" id="auth-forgot">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method POST">POST</span>
                        <span class="path">/api/v1/auth/forgot-password</span>
                        <span class="endpoint-desc">Send password reset link</span>
                        <span class="auth-badge public">Public</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-grid">
                            <div class="body-section">
                                <h4>Request Body</h4>
                                <table>
                                    <thead><tr><th>Field</th><th>Type</th><th>Required</th></tr></thead>
                                    <tbody>
                                        <tr><td><code>email</code></td><td>string (email)</td><td><span class="required">required</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="body-section">
                                <h4>Response <span style="color:var(--get);font-size:11px;">200</span></h4>
                                <pre><span class="json-key">"success"</span>: <span class="json-bool">true</span>,
<span class="json-key">"message"</span>: <span class="json-str">"Password reset link sent"</span></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reset Password -->
                <div class="endpoint" id="auth-reset">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method POST">POST</span>
                        <span class="path">/api/v1/auth/reset-password</span>
                        <span class="endpoint-desc">Complete password reset</span>
                        <span class="auth-badge public">Public</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-grid">
                            <div class="body-section">
                                <h4>Request Body</h4>
                                <table>
                                    <thead><tr><th>Field</th><th>Type</th><th>Required</th></tr></thead>
                                    <tbody>
                                        <tr><td><code>token</code></td><td>string</td><td><span class="required">required</span></td></tr>
                                        <tr><td><code>email</code></td><td>string (email)</td><td><span class="required">required</span></td></tr>
                                        <tr><td><code>password</code></td><td>string (min 8)</td><td><span class="required">required</span></td></tr>
                                        <tr><td><code>password_confirmation</code></td><td>string</td><td><span class="required">required</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="body-section">
                                <h4>Response <span style="color:var(--get);font-size:11px;">200</span></h4>
                                <pre><span class="json-key">"success"</span>: <span class="json-bool">true</span>,
<span class="json-key">"message"</span>: <span class="json-str">"Password reset successfully"</span></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── SUBSCRIPTIONS ─── -->
            <div class="section">
                <div class="section-header">
                    <div class="section-icon">📋</div>
                    <div>
                        <h2>Subscriptions</h2>
                        <div class="section-desc">Manage user subscriptions — all endpoints require authentication</div>
                    </div>
                </div>

                <!-- List -->
                <div class="endpoint" id="subs-list">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method GET">GET</span>
                        <span class="path">/api/v1/subscriptions</span>
                        <span class="endpoint-desc">List all subscriptions</span>
                        <span class="auth-badge protected">Protected</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-grid">
                            <div class="body-section">
                                <h4>Query Parameters</h4>
                                <table>
                                    <thead><tr><th>Param</th><th>Type</th><th></th></tr></thead>
                                    <tbody>
                                        <tr><td><code>page</code></td><td>integer</td><td><span class="optional">optional</span></td></tr>
                                        <tr><td><code>per_page</code></td><td>integer</td><td><span class="optional">optional</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="body-section">
                                <h4>Response <span style="color:var(--get);font-size:11px;">200</span></h4>
                                <pre><span class="json-key">"data"</span>: [
  {
    <span class="json-key">"id"</span>: <span class="json-num">1</span>,
    <span class="json-key">"name"</span>: <span class="json-str">"Netflix"</span>,
    <span class="json-key">"amount"</span>: <span class="json-num">549.00</span>,
    <span class="json-key">"currency"</span>: <span class="json-str">"PHP"</span>,
    <span class="json-key">"billing_cycle"</span>: <span class="json-str">"monthly"</span>,
    <span class="json-key">"next_billing_date"</span>: <span class="json-str">"2025-05-01"</span>
  }
],
<span class="json-key">"meta"</span>: { <span class="json-null">/* pagination */</span> }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create -->
                <div class="endpoint" id="subs-create">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method POST">POST</span>
                        <span class="path">/api/v1/subscriptions</span>
                        <span class="endpoint-desc">Create a subscription</span>
                        <span class="auth-badge protected">Protected</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-grid">
                            <div class="body-section">
                                <h4>Request Body</h4>
                                <table>
                                    <thead><tr><th>Field</th><th>Type</th><th>Required</th></tr></thead>
                                    <tbody>
                                        <tr><td><code>name</code></td><td>string</td><td><span class="required">required</span></td></tr>
                                        <tr><td><code>amount</code></td><td>numeric</td><td><span class="required">required</span></td></tr>
                                        <tr><td><code>currency</code></td><td>string</td><td><span class="required">required</span></td></tr>
                                        <tr><td><code>billing_cycle</code></td><td>string</td><td><span class="required">required</span></td></tr>
                                        <tr><td><code>next_billing_date</code></td><td>date</td><td><span class="required">required</span></td></tr>
                                        <tr><td><code>description</code></td><td>string</td><td><span class="optional">optional</span></td></tr>
                                        <tr><td><code>category</code></td><td>string</td><td><span class="optional">optional</span></td></tr>
                                        <tr><td><code>notify_days_before</code></td><td>integer</td><td><span class="optional">optional</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="body-section">
                                <h4>Response <span style="color:var(--get);font-size:11px;">201</span></h4>
                                <pre><span class="json-key">"success"</span>: <span class="json-bool">true</span>,
<span class="json-key">"message"</span>: <span class="json-str">"Subscription created"</span>,
<span class="json-key">"data"</span>: {
  <span class="json-key">"id"</span>: <span class="json-num">5</span>,
  <span class="json-key">"name"</span>: <span class="json-str">"Spotify"</span>,
  <span class="json-null">/* ... full subscription object */</span>
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Show -->
                <div class="endpoint" id="subs-show">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method GET">GET</span>
                        <span class="path">/api/v1/subscriptions/<span class="param">{id}</span></span>
                        <span class="endpoint-desc">Get a subscription</span>
                        <span class="auth-badge protected">Protected</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-section">
                            <h4>Path Parameters</h4>
                            <table>
                                <thead><tr><th>Param</th><th>Type</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr><td><code>id</code></td><td>integer</td><td>Subscription ID</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Update -->
                <div class="endpoint" id="subs-update">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method PUT">PUT</span>
                        <span class="path">/api/v1/subscriptions/<span class="param">{id}</span></span>
                        <span class="endpoint-desc">Update a subscription</span>
                        <span class="auth-badge protected">Protected</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-section">
                            <h4>Request Body</h4>
                            <p style="color:var(--muted);font-size:12px;margin-bottom:8px">Same fields as Create — all optional.</p>
                            <table>
                                <thead><tr><th>Field</th><th>Type</th><th>Required</th></tr></thead>
                                <tbody>
                                    <tr><td><code>name</code></td><td>string</td><td><span class="optional">optional</span></td></tr>
                                    <tr><td><code>amount</code></td><td>numeric</td><td><span class="optional">optional</span></td></tr>
                                    <tr><td><code>currency</code></td><td>string</td><td><span class="optional">optional</span></td></tr>
                                    <tr><td><code>billing_cycle</code></td><td>string</td><td><span class="optional">optional</span></td></tr>
                                    <tr><td><code>next_billing_date</code></td><td>date</td><td><span class="optional">optional</span></td></tr>
                                    <tr><td><code>description</code></td><td>string</td><td><span class="optional">optional</span></td></tr>
                                    <tr><td><code>category</code></td><td>string</td><td><span class="optional">optional</span></td></tr>
                                    <tr><td><code>notify_days_before</code></td><td>integer</td><td><span class="optional">optional</span></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Delete -->
                <div class="endpoint" id="subs-delete">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method DELETE">DELETE</span>
                        <span class="path">/api/v1/subscriptions/<span class="param">{id}</span></span>
                        <span class="endpoint-desc">Delete a subscription</span>
                        <span class="auth-badge protected">Protected</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-section">
                            <h4>Response <span style="color:var(--get);font-size:11px;">200</span></h4>
                            <pre><span class="json-key">"success"</span>: <span class="json-bool">true</span>,
<span class="json-key">"message"</span>: <span class="json-str">"Subscription deleted"</span></pre>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="endpoint" id="subs-summary">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method GET">GET</span>
                        <span class="path">/api/v1/subscriptions/summary</span>
                        <span class="endpoint-desc">Cost analytics summary</span>
                        <span class="auth-badge protected">Protected</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-section">
                            <h4>Response <span style="color:var(--get);font-size:11px;">200</span></h4>
                            <pre><span class="json-key">"data"</span>: {
  <span class="json-key">"total_monthly"</span>: <span class="json-num">1250.00</span>,
  <span class="json-key">"total_yearly"</span>: <span class="json-num">15000.00</span>,
  <span class="json-key">"currency"</span>: <span class="json-str">"PHP"</span>,
  <span class="json-key">"active_count"</span>: <span class="json-num">8</span>,
  <span class="json-key">"by_category"</span>: [ <span class="json-null">/* grouped totals */</span> ]
}</pre>
                        </div>
                    </div>
                </div>

                <!-- Upcoming -->
                <div class="endpoint" id="subs-upcoming">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method GET">GET</span>
                        <span class="path">/api/v1/subscriptions/upcoming</span>
                        <span class="endpoint-desc">Upcoming renewals</span>
                        <span class="auth-badge protected">Protected</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-grid">
                            <div class="body-section">
                                <h4>Query Parameters</h4>
                                <table>
                                    <thead><tr><th>Param</th><th>Type</th><th></th></tr></thead>
                                    <tbody>
                                        <tr><td><code>days</code></td><td>integer</td><td><span class="optional">optional (default 7)</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="body-section">
                                <h4>Response <span style="color:var(--get);font-size:11px;">200</span></h4>
                                <pre><span class="json-key">"data"</span>: [
  {
    <span class="json-key">"id"</span>: <span class="json-num">3</span>,
    <span class="json-key">"name"</span>: <span class="json-str">"Adobe CC"</span>,
    <span class="json-key">"next_billing_date"</span>: <span class="json-str">"2025-04-26"</span>,
    <span class="json-key">"days_until"</span>: <span class="json-num">3</span>
  }
]</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── NOTIFICATIONS ─── -->
            <div class="section">
                <div class="section-header">
                    <div class="section-icon">🔔</div>
                    <div>
                        <h2>Notifications</h2>
                        <div class="section-desc">Renewal reminders and alerts — all endpoints require authentication</div>
                    </div>
                </div>

                <!-- List -->
                <div class="endpoint" id="notif-list">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method GET">GET</span>
                        <span class="path">/api/v1/notifications</span>
                        <span class="endpoint-desc">List notifications (paginated)</span>
                        <span class="auth-badge protected">Protected</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-grid">
                            <div class="body-section">
                                <h4>Query Parameters</h4>
                                <table>
                                    <thead><tr><th>Param</th><th>Type</th><th></th></tr></thead>
                                    <tbody>
                                        <tr><td><code>page</code></td><td>integer</td><td><span class="optional">optional</span></td></tr>
                                        <tr><td><code>per_page</code></td><td>integer</td><td><span class="optional">optional</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="body-section">
                                <h4>Response <span style="color:var(--get);font-size:11px;">200</span></h4>
                                <pre><span class="json-key">"data"</span>: [
  {
    <span class="json-key">"id"</span>: <span class="json-num">10</span>,
    <span class="json-key">"message"</span>: <span class="json-str">"Netflix renews in 3 days"</span>,
    <span class="json-key">"is_read"</span>: <span class="json-bool">false</span>,
    <span class="json-key">"created_at"</span>: <span class="json-str">"2025-04-23T08:00:00Z"</span>
  }
],
<span class="json-key">"meta"</span>: { <span class="json-null">/* pagination */</span> }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unread count -->
                <div class="endpoint" id="notif-unread">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method GET">GET</span>
                        <span class="path">/api/v1/notifications/unread-count</span>
                        <span class="endpoint-desc">Get unread notification count</span>
                        <span class="auth-badge protected">Protected</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-section">
                            <h4>Response <span style="color:var(--get);font-size:11px;">200</span></h4>
                            <pre><span class="json-key">"data"</span>: {
  <span class="json-key">"unread_count"</span>: <span class="json-num">4</span>
}</pre>
                        </div>
                    </div>
                </div>

                <!-- Mark read -->
                <div class="endpoint" id="notif-read">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method PATCH">PATCH</span>
                        <span class="path">/api/v1/notifications/<span class="param">{id}</span>/read</span>
                        <span class="endpoint-desc">Mark notification as read</span>
                        <span class="auth-badge protected">Protected</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-section">
                            <h4>Response <span style="color:var(--get);font-size:11px;">200</span></h4>
                            <pre><span class="json-key">"success"</span>: <span class="json-bool">true</span>,
<span class="json-key">"message"</span>: <span class="json-str">"Notification marked as read"</span></pre>
                        </div>
                    </div>
                </div>

                <!-- Read all -->
                <div class="endpoint" id="notif-read-all">
                    <div class="endpoint-header" onclick="toggle(this)">
                        <span class="method PATCH">PATCH</span>
                        <span class="path">/api/v1/notifications/read-all</span>
                        <span class="endpoint-desc">Mark all notifications as read</span>
                        <span class="auth-badge protected">Protected</span>
                        <span class="chevron">▼</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="body-section">
                            <h4>Response <span style="color:var(--get);font-size:11px;">200</span></h4>
                            <pre><span class="json-key">"success"</span>: <span class="json-bool">true</span>,
<span class="json-key">"message"</span>: <span class="json-str">"All notifications marked as read"</span></pre>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /content -->
    </div><!-- /main -->
</div><!-- /layout -->

<script>
    function toggle(header) {
        header.closest('.endpoint').classList.toggle('open');
    }
</script>
</body>
</html>
