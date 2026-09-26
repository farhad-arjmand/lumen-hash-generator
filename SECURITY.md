# Security

The v2 rewrite uses PHP's cryptographically secure random functions and enforces at least 128 bits of random entropy for generated tokens. It is not an audited authentication platform. The legacy time-based implementation should not be used for security-sensitive tokens.

Report suspected vulnerabilities privately to the maintainer at **farhadarjmand@gmail.com**. Include the affected version/commit, a minimal reproduction and impact. Do not include real secrets or customer data in an issue.

Scope: token generation and digest verification. Host applications remain responsible for access control, expiration, storage, revocation, one-time use, transport security and logging policy. The package does not promise token uniqueness or password-hardening.
