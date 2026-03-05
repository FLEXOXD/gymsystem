        // SuperAdmin-only visual sync:
        // if top-right avatar exists, mirror it into the sidebar brand badge.
        const isSuperAdminViewer = <?php echo json_encode($isSuperAdmin, 15, 512) ?>;
        if (isSuperAdminViewer) {
            const brandLogoBadge = document.getElementById('brand-logo-badge');
            const topAvatarImage = document.getElementById('user-avatar-image') || document.querySelector('#user-menu-button img');
            const topAvatarSrc = topAvatarImage?.getAttribute('src') || '';
            if (brandLogoBadge && topAvatarSrc !== '') {
                brandLogoBadge.innerHTML = '';
                const img = document.createElement('img');
                img.src = topAvatarSrc;
                img.alt = topAvatarImage?.getAttribute('alt') || 'SuperAdmin';
                img.className = 'brand-logo-media brand-logo-media-cover';
                brandLogoBadge.appendChild(img);
            }
        }

        const legalOverlay = document.getElementById('legal-acceptance-overlay');
        const legalForm = document.getElementById('legal-accept-form');
        const legalCheckbox = document.getElementById('legal-accept-checkbox');
        const legalSubmit = document.getElementById('legal-accept-submit');
        if (legalOverlay && legalForm && legalCheckbox && legalSubmit) {
            document.body.style.overflow = 'hidden';

            const permissionField = document.getElementById('legal-location-permission');
            const latitudeField = document.getElementById('legal-location-latitude');
            const longitudeField = document.getElementById('legal-location-longitude');
            const accuracyField = document.getElementById('legal-location-accuracy');
            let submitting = false;

            const setPermission = function (value) {
                if (!permissionField) return;
                permissionField.value = String(value || 'skipped');
            };

            const setCoordinates = function (latitude, longitude, accuracy) {
                if (latitudeField) latitudeField.value = latitude;
                if (longitudeField) longitudeField.value = longitude;
                if (accuracyField) accuracyField.value = accuracy;
            };

            const applyButtonState = function () {
                legalSubmit.disabled = !legalCheckbox.checked || submitting;
            };

            const finalizeSubmit = function () {
                if (submitting) return;
                submitting = true;
                applyButtonState();
                legalForm.submit();
            };

            legalCheckbox.addEventListener('change', applyButtonState);
            applyButtonState();

            legalForm.addEventListener('submit', function (event) {
                event.preventDefault();
                if (!legalCheckbox.checked || submitting) {
                    applyButtonState();
                    return;
                }

                if (!('geolocation' in navigator)) {
                    setPermission('unavailable');
                    setCoordinates('', '', '');
                    finalizeSubmit();
                    return;
                }

                let settled = false;
                const resolve = function (permission, position) {
                    if (settled) return;
                    settled = true;
                    setPermission(permission);
                    if (position && position.coords) {
                        setCoordinates(
                            String(position.coords.latitude ?? ''),
                            String(position.coords.longitude ?? ''),
                            String(position.coords.accuracy ?? '')
                        );
                    } else {
                        setCoordinates('', '', '');
                    }
                    finalizeSubmit();
                };

                try {
                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            resolve('granted', position);
                        },
                        function (error) {
                            if (error && error.code === 1) {
                                resolve('denied', null);
                                return;
                            }
                            if (error && error.code === 2) {
                                resolve('unavailable', null);
                                return;
                            }
                            resolve('error', null);
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 5500,
                            maximumAge: 0
                        }
                    );
                } catch (_error) {
                    resolve('error', null);
                }

                window.setTimeout(function () {
                    if (!settled) {
                        resolve('error', null);
                    }
                }, 6500);
            });
        }
<?php /**PATH C:\laragon\www\gymsystem\resources\views/layouts/partials/panel-inline/superadmin-legal.blade.php ENDPATH**/ ?>