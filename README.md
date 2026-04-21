# TrustDPV PHP SDK

Official PHP SDK for the [TrustDPV](https://trustdpv.com) API.

## Install

```bash
composer require trustdpv/sdk
```

Or include directly:

```php
require_once 'TrustDPV.php';
```

## Quick Start

```php
$tdpv = new TrustDPV('tdpv_live_your_key_here');

// Verify a user
$result = $tdpv->verify('trustdpv');
// ['valid' => true, 'username' => 'trustdpv', 'trust_score' => 660, 'verified' => true]

// Full profile
$profile = $tdpv->profile('trustdpv');

// Batch verify
$batch = $tdpv->verifyBatch(['seller1', 'seller2', 'seller3']);

// Badge URL (for <img> tags)
$badge = $tdpv->badgeUrl('trustdpv');
// https://trustdpv.com/badge/trustdpv.svg
```

## API

| Method | Description |
|--------|-------------|
| `verify($username)` | Lightweight trust check |
| `profile($username)` | Full public profile |
| `verifyBatch($usernames)` | Batch verify (max 50) |
| `badgeUrl($username)` | SVG badge image URL |
| `profileUrl($username)` | Public profile page URL |
| `health()` | API health check |

## Requirements

- PHP 7.4+
- ext-curl

## License

MIT