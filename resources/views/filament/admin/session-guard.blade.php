<script>
    document.addEventListener('livewire:init', () => {
        let redirectInProgress = false;

        const redirectOnce = (target) => {
            if (redirectInProgress || !target) {
                return;
            }

            try {
                const url = new URL(target, window.location.origin);

                if (url.origin !== window.location.origin) {
                    return;
                }

                redirectInProgress = true;
                window.location.assign(url.href);
            } catch (_) {
                // Ignore malformed redirect targets and let Livewire use its normal handling.
            }
        };

        Livewire.hook('request', ({ respond, fail }) => {
            respond(({ response }) => {
                if (!response?.redirected) {
                    return;
                }

                try {
                    const target = new URL(response.url);

                    if (
                        target.origin === window.location.origin
                        && (
                            target.pathname === '/admin/login'
                            || target.pathname.startsWith('/admin-security/challenge')
                        )
                    ) {
                        redirectOnce(target.href);
                    }
                } catch (_) {
                    // Leave unrelated redirects to Livewire.
                }
            });

            fail(({ status, content, preventDefault }) => {
                // Do not convert Livewire's native 419/session-expired handling into a
                // forced admin-login redirect. Doing so masked CSRF/session failures as
                // MFA failures and made debugging/recovery substantially worse.
                if (status !== 401) {
                    return;
                }

                try {
                    const payload = JSON.parse(content);

                    if (payload?.code === 'ADMIN_MFA_REQUIRED' && payload?.redirect) {
                        preventDefault();
                        redirectOnce(payload.redirect);
                    }
                } catch (_) {
                    // A non-MFA 401 should keep Livewire's default error handling.
                }
            });
        });
    });
</script>
