# Security Policy

## Supported versions

Security fixes are applied to the latest tagged release of `voodflow/vcookiebar` on
the `main` branch. Older releases may be asked to upgrade before receiving a
fix.

## Reporting a vulnerability

Do not open a public GitHub issue for a suspected security vulnerability.

Email **dev@voodflow.com** with `Vcookiebar security` in the subject. Include:

- the affected package version or commit;
- a description of the vulnerability and its impact;
- reproducible steps or a proof of concept, when safe to share privately;
- any known mitigations or public disclosures.

We aim to acknowledge reports within three business days. Please allow a
reasonable remediation window before public disclosure.

## Scope

In scope are vulnerabilities in code shipped by `voodflow/vcookiebar`, including
authorization bypasses, cross-site scripting, cross-site request forgery, and
consent endpoint abuse.

Issues caused exclusively by a compromised host application, insecure
deployment configuration, or third-party packages not maintained in this
repository are outside this policy's scope.
