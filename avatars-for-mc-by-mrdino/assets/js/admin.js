(function ($) {

    $(document).ready(function () {
        console.log('MC Avatars admin.js loaded');

        const urlParams  = new URLSearchParams(window.location.search);
        const action     = urlParams.get('action');

        const $wpLoginField = $('#user_login');   // login normal WP
        const $wcLoginField = $('#username');     // login WooCommerce

        const isProfile  = $('#mc_username').length > 0;
        const isRegister = (action === 'register');
        const isLogin    = (!isProfile && !isRegister && ($wpLoginField.length > 0 || $wcLoginField.length > 0));


        // Solo actuamos en perfil, registro o login
        if (!isProfile && !isRegister && !isLogin) {
            return;
        }

        const presets = [
            { key: 'mhf_steve',   label: 'Steve (Default Player)',   username: 'MHF_Steve' },
            { key: 'mhf_alex',    label: 'Alex (Default Player)',    username: 'MHF_Alex' },
            { key: 'mhf_creeper', label: 'Creeper',                  username: 'MHF_Creeper' },
            { key: 'mhf_zombie',  label: 'Zombie',                   username: 'MHF_Zombie' },
            { key: 'mhf_skeleton',label: 'Skeleton',                 username: 'MHF_Skeleton' },
            { key: 'mhf_enderman',label: 'Enderman',                 username: 'MHF_Enderman' }
        ];

        const $usernameInput = isProfile
            ? $('#mc_username')
            : (isLogin ? ($wpLoginField.length ? $wpLoginField : $wcLoginField) : $('#user_login'));


        /* --------------------------------------------------
         *  PREVIEW: crear imagen de preview según contexto
         * -------------------------------------------------- */
        if ($('#mc-avatar-preview').length === 0) {

            if (isProfile) {
                $usernameInput.closest('td').append(
                    '<div class="mc-avatar-preview-wrapper" style="margin-top: 10px;">' +
                        '<strong>Minecraft Avatar Preview</strong><br>' +
                        '<img id="mc-avatar-preview" ' +
                             'src="https://mc-heads.net/avatar/MHF_Steve/128" ' +
                             'alt="Minecraft Avatar Preview" ' +
                             'class="mc-avatar-preview-img">' +
                    '</div>'
                );
            }

            if (isRegister) {
                $usernameInput.after(
                    '<div class="mc-avatar-preview-wrapper" style="margin-top: 10px; text-align:center;">' +
                        '<strong>Minecraft Avatar Preview</strong><br>' +
                        '<img id="mc-avatar-preview" ' +
                             'src="https://mc-heads.net/avatar/MHF_Steve/128" ' +
                             'alt="Minecraft Avatar Preview" ' +
                             'class="mc-avatar-preview-img">' +
                    '</div>'
                );
            }

            if (isLogin) {
                const $loginField = $wpLoginField.length ? $wpLoginField : $wcLoginField;
                $loginField.after(
                    '<div class="mc-avatar-preview-wrapper" style="margin-top:10px; text-align:center;">' +
                        '<strong>Your Avatar</strong><br>' +
                        '<img id="mc-avatar-preview" ' +
                             'src="https://mc-heads.net/avatar/MHF_Steve/128" ' +
                             'style="width:96px;height:96px;margin-top:6px;image-rendering:pixelated;">' +
                    '</div>'
                );
            }

        }

        const $previewImg   = $('#mc-avatar-preview');
        const $presetHidden = $('#mc_avatar_preset');

        /* --------------------------------------------------
         *  GRID EN PERFIL: presets + custom skins
         * -------------------------------------------------- */
        if (isProfile) {
            const $grid       = $('#mc-avatar-preset-grid');
            const selectedKey = $presetHidden.val() || '';
            let   html        = '';

            // Presets globales
            presets.forEach(p => {
                const isSelected = (p.key === selectedKey);
                html +=
                    '<button type="button" ' +
                        'class="mc-avatar-preset' + (isSelected ? ' is-selected' : '') + '" ' +
                        'data-key="' + p.key + '" ' +
                        'data-username="' + p.username + '" ' +
                        'title="' + p.label + '">' +
                            '<img src="https://mc-heads.net/avatar/' + p.username + '/64" alt="' + p.label + '">' +
                    '</button>';
            });

            // Custom skins del usuario (añadidas al final)
            const rawCustom = $('#mc_avatar_custom_skins').val();
            let customSkins = [];
            try {
                if (rawCustom) {
                    customSkins = JSON.parse(rawCustom);
                    if (!Array.isArray(customSkins)) {
                        customSkins = [];
                    }
                }
            } catch (e) {
                customSkins = [];
            }

            customSkins.forEach((url, index) => {
                const key       = 'custom:' + index;
                const isSelected = (key === selectedKey);
                html +=
                    '<button type="button" ' +
                        'class="mc-avatar-preset mc-avatar-preset-custom' + (isSelected ? ' is-selected' : '') + '" ' +
                        'data-key="' + key + '" ' +
                        'data-custom-url="' + url + '" ' +
                        'title="Custom Skin ' + (index + 1) + '">' +
                            '<img src="' + url + '" alt="Custom Skin ' + (index + 1) + '">' +
                    '</button>';
            });

            $grid.html(html);
        }

        /* --------------------------------------------------
         *  GRID EN REGISTRO: solo presets (sin custom aún)
         * -------------------------------------------------- */
        if (isRegister) {
            if ($('#mc_avatar_preset').length === 0) {
                // Por si acaso, pero en teoría existe en el markup de perfil, no en registro
            }

            // Crear grid bajo el preview
            if ($('.mc-avatar-preset-grid').length === 0) {
                let html =
                    '<div class="mc-avatar-preset-wrapper" style="margin-top: 12px; text-align:center;">' +
                        '<div class="mc-avatar-preset-grid">';

                presets.forEach(p => {
                    html +=
                        '<button type="button" ' +
                            'class="mc-avatar-preset" ' +
                            'data-key="' + p.key + '" ' +
                            'data-username="' + p.username + '" ' +
                            'title="' + p.label + '">' +
                                '<img src="https://mc-heads.net/avatar/' + p.username + '/64" alt="' + p.label + '">' +
                        '</button>';
                });

                html +=
                        '</div>' +
                        '<input type="hidden" name="mc_avatar_preset" id="mc_avatar_preset" value="">' +
                        '<p class="description" style="font-size:12px;margin-top:6px;">' +
                            'Click a head to use it as your avatar. Click again to deselect.' +
                        '</p>' +
                    '</div>';

                $('.mc-avatar-preview-wrapper').after(html);
            }
        }

        /* --------------------------------------------------
         *  FUNCIÓN: URL de preview (perfil/registro)
         * -------------------------------------------------- */
        function getPreviewUrlProfileRegister() {

            const $selected = $('.mc-avatar-preset.is-selected').first();

            // Custom skin seleccionada
            if ($selected.length && $selected.hasClass('mc-avatar-preset-custom')) {
                const customUrl = $selected.data('customUrl');
                if (customUrl) {
                    const sep = customUrl.indexOf('?') === -1 ? '?' : '&';
                    return customUrl + sep + 'cb=' + Date.now();
                }
            }

            // Preset basado en username
            if ($selected.length && $selected.data('username')) {
                const presetUsername = String($selected.data('username') || '').trim();
                const mcName = presetUsername.length < 1 ? 'MHF_Steve' : presetUsername.toLowerCase();
                return 'https://mc-heads.net/avatar/' + encodeURIComponent(mcName) + '/128?cb=' + Date.now();
            }

            // Sin preset: usar el username del campo
            const username = $usernameInput.val().trim();
            const mcName   = username.length < 1 ? 'MHF_Steve' : username.toLowerCase();
            return 'https://mc-heads.net/avatar/' + encodeURIComponent(mcName) + '/128?cb=' + Date.now();
        }

        function updatePreviewProfileRegister() {
            const url = getPreviewUrlProfileRegister();
            $previewImg.attr('src', url);
        }

                /* --------------------------------------------------
                 *  LOGIN: preview usando AJAX y avatar real
                 * -------------------------------------------------- */
                if (isLogin) {

                    const $loginField = $wpLoginField.length ? $wpLoginField : $wcLoginField;

                    $loginField.on('input', function () {
                        const val = $(this).val().trim();

                        if (val.length < 1) {
                            $previewImg.attr('src', 'https://mc-heads.net/avatar/MHF_Steve/128');
                            return;
                        }

                        // URL y nonce desde mcAvatarsData (localize_script)
                        const ajaxUrl = (window.mcAvatarsData && mcAvatarsData.ajaxUrl) ? mcAvatarsData.ajaxUrl : (window.ajaxurl || '');
                        const nonce   = (window.mcAvatarsData && mcAvatarsData.nonce)   ? mcAvatarsData.nonce   : '';

                        if (!ajaxUrl) {
                            return;
                        }

                        $.get(
                            ajaxUrl,
                            {
                                action: 'mc_avatars_lookup',
                                user:   val,
                                _mc_avatars_nonce: nonce
                            },
                            function (resp) {
                                if (resp && resp.avatar) {
                                    $previewImg.attr('src', resp.avatar);
                                }
                            }
                        );
                    });
                }

        /* --------------------------------------------------
         *  EVENTOS GLOBALES
         * -------------------------------------------------- */
        // Click en cabezas
        $(document).on('click', '.mc-avatar-preset', function () {
            const $btn = $(this);
            const key  = $btn.data('key');

            if ($btn.hasClass('is-selected')) {
                $btn.removeClass('is-selected');
                $presetHidden.val('');
            } else {
                $('.mc-avatar-preset').removeClass('is-selected');
                $btn.addClass('is-selected');
                if ($presetHidden.length) {
                    $presetHidden.val(key || '');
                }
            }

            if (!isLogin) {
                updatePreviewProfileRegister();
            }
        });

        // Cambios en username en perfil/registro
        if (!isLogin) {
            $usernameInput.on('input', function () {
                updatePreviewProfileRegister();
            });
        }

        // Primera carga de preview
        if (!isLogin) {
            updatePreviewProfileRegister();
        }
    });

})(jQuery);
