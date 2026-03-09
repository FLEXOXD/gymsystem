const OPEN_ICON = `
<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" stroke="currentColor" stroke-width="1.8"></path>
    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"></circle>
</svg>
`;

const CLOSED_ICON = `
<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
    <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.8"></path>
    <path d="M10.5 6.3A11 11 0 0 1 12 6c6.5 0 10 6 10 6a17 17 0 0 1-4.1 4.8M6.6 8.1C3.8 10 2 12 2 12s3.5 6 10 6a11 11 0 0 0 4.3-.8" stroke="currentColor" stroke-width="1.8"></path>
</svg>
`;

let generatedInputIds = 0;

const hasBuiltInToggle = (input) => {
    const parent = input.parentElement;
    if (!parent) {
        return false;
    }

    return Boolean(
        parent.querySelector('.toggle-password')
        || parent.querySelector('[data-password-toggle-button]')
        || parent.querySelector('[data-password-toggle]')
    );
};

const createToggleButton = (input) => {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'ui-password-toggle-button';
    button.setAttribute('data-password-toggle-button', '1');
    button.setAttribute('aria-controls', input.id);
    button.setAttribute('aria-label', 'Mostrar contraseña');
    button.setAttribute('aria-pressed', 'false');
    button.innerHTML = `
        <span class="ui-password-icon ui-password-icon-open is-hidden">${OPEN_ICON}</span>
        <span class="ui-password-icon ui-password-icon-closed">${CLOSED_ICON}</span>
    `;

    const iconOpen = button.querySelector('.ui-password-icon-open');
    const iconClosed = button.querySelector('.ui-password-icon-closed');

    const syncState = () => {
        const visible = input.type === 'text';
        button.setAttribute('aria-pressed', visible ? 'true' : 'false');
        button.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
        iconOpen?.classList.toggle('is-hidden', !visible);
        iconClosed?.classList.toggle('is-hidden', visible);
    };

    button.addEventListener('click', () => {
        input.type = input.type === 'password' ? 'text' : 'password';
        syncState();
    });

    syncState();

    return button;
};

const enhancePasswordInput = (input) => {
    if (!(input instanceof HTMLInputElement)) {
        return;
    }
    if (input.dataset.passwordToggleReady === '1') {
        return;
    }
    if (input.hasAttribute('data-password-toggle-ignore') || input.closest('[data-password-toggle-ignore]')) {
        input.dataset.passwordToggleReady = '1';
        return;
    }
    if (hasBuiltInToggle(input)) {
        input.dataset.passwordToggleReady = '1';
        return;
    }

    if (!input.id) {
        generatedInputIds += 1;
        input.id = 'password-toggle-input-' + String(generatedInputIds);
    }

    const wrapper = document.createElement('span');
    wrapper.className = 'ui-password-field';
    wrapper.setAttribute('data-password-toggle-wrapper', '1');

    const parent = input.parentNode;
    if (!parent) {
        return;
    }

    parent.insertBefore(wrapper, input);
    wrapper.appendChild(input);
    input.classList.add('ui-password-input');

    const button = createToggleButton(input);
    wrapper.appendChild(button);
    input.dataset.passwordToggleReady = '1';
};

const scan = (root) => {
    if (!(root instanceof Element || root instanceof Document)) {
        return;
    }

    const inputs = root.querySelectorAll('input[type="password"]');
    inputs.forEach((input) => {
        enhancePasswordInput(input);
    });
};

export const initPasswordVisibilityModule = () => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            scan(document);
        }, { once: true });
    } else {
        scan(document);
    }
};

