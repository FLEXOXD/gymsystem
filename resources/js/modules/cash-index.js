function formatMoney(currencySymbol, value) {
    const num = Number(value || 0);
    return (num < 0 ? '-' : '') + currencySymbol + Math.abs(num).toFixed(2);
}

function openModal(element) {
    if (!element) {
        return;
    }

    element.classList.remove('hidden');
}

function closeModal(element) {
    if (!element) {
        return;
    }

    element.classList.add('hidden');
}

function setTone(element, value) {
    if (!element) {
        return;
    }

    const tone = value > 0 ? 'warn' : (value < 0 ? 'bad' : 'ok');
    element.setAttribute('data-tone', tone);
}

export function initCashIndexModule() {
    const root = document.querySelector('[data-module="cash-index"]');
    if (!root) {
        return;
    }

    const currencySymbol = root.getAttribute('data-currency-symbol') || '$';
    const voidRouteTemplate = root.getAttribute('data-void-route-template') || '';

    const movementForm = document.getElementById('cash-movement-form');
    const movementType = document.getElementById('movement-type');
    const movementMethod = document.getElementById('movement-method');
    const movementDescription = document.getElementById('movement-description');
    const movementDescriptionLabel = document.getElementById('movement-description-label');
    const movementCategoryWrap = document.getElementById('movement-expense-category-wrap');
    const movementSubmit = document.getElementById('movement-submit');
    const movementFormHint = document.getElementById('movement-form-hint');
    const movementAmount = document.getElementById('movement-amount');
    const highConfirmed = document.getElementById('movement-high-confirmed');
    const guardAlert = document.getElementById('movement-guard-alert');

    const highAmountModal = document.getElementById('high-amount-modal');
    const highAmountValue = document.getElementById('high-amount-value');
    const highAmountConfirm = document.getElementById('confirm-high-amount');

    const closeForm = document.getElementById('cash-close-form');
    const closeStatusText = document.getElementById('close-status-text');
    const differenceCash = document.getElementById('difference-cash');
    const differenceCard = document.getElementById('difference-card');
    const differenceTransfer = document.getElementById('difference-transfer');
    const differenceTotal = document.getElementById('difference-total');
    const differenceTotalCard = document.getElementById('difference-total-card');
    const differenceReason = document.getElementById('difference-reason');
    const differenceReasonWrap = document.getElementById('difference-reason-wrap');
    const closeBalanceInput = document.getElementById('close-closing-balance');
    const closeAlert = document.getElementById('close-form-alert');
    const differenceApproved = document.getElementById('close-difference-approved');

    const differenceApprovalModal = document.getElementById('difference-approval-modal');
    const differenceApprovalPassword = document.getElementById('difference-approval-password');
    const differenceApprovalError = document.getElementById('difference-approval-error');
    const confirmCloseWithDiff = document.getElementById('confirm-close-with-diff');

    const voidModal = document.getElementById('void-movement-modal');
    const voidLabel = document.getElementById('void-movement-label');
    const voidForm = document.getElementById('void-movement-form');
    const monthlyMovementsModal = document.getElementById('monthly-movements-modal');
    const openMonthlyMovementsModalButton = document.getElementById('open-monthly-movements-modal');

    function updateMovementSubmitState() {
        const hasType = Boolean(movementType?.value);
        const hasMethod = Boolean(movementMethod?.value);
        const amount = Number(movementAmount?.value || 0);
        const hasAmount = amount > 0;
        const hasDescription = (movementDescription?.value || '').trim() !== '';
        const isValid = hasType && hasMethod && hasAmount && hasDescription;

        if (movementSubmit) {
            movementSubmit.disabled = !isValid;
            movementSubmit.classList.toggle('opacity-60', !isValid);
            movementSubmit.classList.toggle('cursor-not-allowed', !isValid);
        }

        if (movementFormHint) {
            movementFormHint.textContent = isValid
                ? 'Listo para registrar el movimiento.'
                : 'Completa tipo, metodo, monto y descripcion para habilitar el registro.';
            movementFormHint.setAttribute('data-tone', isValid ? 'ok' : 'warn');
        }
    }

    function updateMovementMode() {
        if (!movementType) {
            return;
        }

        const isExpense = movementType.value === 'expense';

        if (movementDescription) {
            movementDescription.required = true;
            movementDescription.placeholder = isExpense
                ? 'Motivo obligatorio del egreso.'
                : 'Ingresa descripcion obligatoria.';
        }

        if (movementDescriptionLabel) {
            movementDescriptionLabel.textContent = 'Descripcion (obligatoria)';
        }

        if (movementCategoryWrap) {
            movementCategoryWrap.classList.toggle('hidden', !isExpense);
        }

        if (movementSubmit) {
            movementSubmit.textContent = isExpense ? 'Registrar egreso' : 'Registrar ingreso';
            movementSubmit.classList.toggle('ui-button-danger', isExpense);
            movementSubmit.classList.toggle('ui-button-success', !isExpense);
        }

        if (guardAlert) {
            guardAlert.classList.add('hidden');
            guardAlert.textContent = '';
        }

        updateMovementSubmitState();
    }

    movementType?.addEventListener('change', updateMovementMode);
    movementMethod?.addEventListener('change', updateMovementSubmitState);
    movementAmount?.addEventListener('input', updateMovementSubmitState);
    movementDescription?.addEventListener('input', updateMovementSubmitState);
    updateMovementMode();
    updateMovementSubmitState();

    movementForm?.addEventListener('submit', (event) => {
        const amount = Number(movementAmount?.value || 0);
        const threshold = Number(movementForm.dataset.highThreshold || 100);
        const alreadyConfirmed = highConfirmed?.value === '1';
        const descriptionValue = (movementDescription?.value || '').trim();

        if (!movementType?.value || !movementMethod?.value) {
            event.preventDefault();
            if (guardAlert) {
                guardAlert.classList.remove('hidden');
                guardAlert.textContent = 'Selecciona tipo y metodo antes de registrar.';
            }
            return;
        }

        if (descriptionValue === '') {
            event.preventDefault();
            movementDescription?.focus();
            if (guardAlert) {
                guardAlert.classList.remove('hidden');
                guardAlert.textContent = 'Ingresa descripcion obligatoria.';
            }
            return;
        }

        if (amount <= 0) {
            event.preventDefault();
            if (guardAlert) {
                guardAlert.classList.remove('hidden');
                guardAlert.textContent = 'Monto debe ser mayor a 0.';
            }
            return;
        }

        if (amount > threshold && !alreadyConfirmed) {
            event.preventDefault();
            if (highAmountValue) {
                highAmountValue.textContent = formatMoney(currencySymbol, amount);
            }
            openModal(highAmountModal);
        }
    });

    highAmountConfirm?.addEventListener('click', () => {
        if (highConfirmed) {
            highConfirmed.value = '1';
        }

        closeModal(highAmountModal);
        movementForm?.submit();
    });

    document.querySelectorAll('[data-close-high-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            closeModal(highAmountModal);
        });
    });

    function updateCloseMath() {
        if (!closeForm) {
            return { totalDiff: 0, totalCounted: 0 };
        }

        const expectedCash = Number(closeForm.dataset.expectedCash || 0);
        const expectedCard = Number(closeForm.dataset.expectedCard || 0);
        const expectedTransfer = Number(closeForm.dataset.expectedTransfer || 0);

        const countedCash = Number(document.getElementById('counted-cash')?.value || 0);
        const countedCard = Number(document.getElementById('counted-card')?.value || 0);
        const countedTransfer = Number(document.getElementById('counted-transfer')?.value || 0);

        const diffCash = Math.round((countedCash - expectedCash) * 100) / 100;
        const diffCard = Math.round((countedCard - expectedCard) * 100) / 100;
        const diffTransfer = Math.round((countedTransfer - expectedTransfer) * 100) / 100;
        const totalDiff = Math.round((diffCash + diffCard + diffTransfer) * 100) / 100;
        const totalCounted = Math.round((countedCash + countedCard + countedTransfer) * 100) / 100;

        if (differenceCash) {
            differenceCash.textContent = formatMoney(currencySymbol, diffCash);
            setTone(differenceCash, diffCash);
        }
        if (differenceCard) {
            differenceCard.textContent = formatMoney(currencySymbol, diffCard);
            setTone(differenceCard, diffCard);
        }
        if (differenceTransfer) {
            differenceTransfer.textContent = formatMoney(currencySymbol, diffTransfer);
            setTone(differenceTransfer, diffTransfer);
        }
        if (differenceTotal) {
            differenceTotal.textContent = formatMoney(currencySymbol, totalDiff);
            setTone(differenceTotal, totalDiff);
        }
        if (differenceTotalCard) {
            differenceTotalCard.setAttribute('data-tone', totalDiff > 0 ? 'warn' : (totalDiff < 0 ? 'bad' : 'ok'));
        }

        if (closeStatusText) {
            if (totalDiff === 0) {
                closeStatusText.textContent = 'CUADRA';
                closeStatusText.setAttribute('data-tone', 'ok');
            } else if (totalDiff > 0) {
                closeStatusText.textContent = `SOBRANTE ${formatMoney(currencySymbol, totalDiff)}`;
                closeStatusText.setAttribute('data-tone', 'warn');
            } else {
                closeStatusText.textContent = `FALTANTE ${formatMoney(currencySymbol, totalDiff)}`;
                closeStatusText.setAttribute('data-tone', 'bad');
            }
        }

        if (differenceReason) {
            differenceReason.required = totalDiff !== 0;
        }
        if (differenceReasonWrap) {
            differenceReasonWrap.classList.toggle('hidden', totalDiff === 0);
        }

        if (closeBalanceInput) {
            closeBalanceInput.value = totalCounted.toFixed(2);
        }

        return { totalDiff, totalCounted };
    }

    ['counted-cash', 'counted-card', 'counted-transfer'].forEach((id) => {
        document.getElementById(id)?.addEventListener('input', updateCloseMath);
    });
    updateCloseMath();

    closeForm?.addEventListener('submit', (event) => {
        const calc = updateCloseMath();
        const canApprove = (closeForm.dataset.canApproveDifference || '0') === '1';
        const hasDifference = Math.abs(calc.totalDiff) > 0.0001;

        if (closeAlert) {
            closeAlert.classList.add('hidden');
            closeAlert.textContent = '';
        }

        if (!hasDifference) {
            if (differenceApproved) {
                differenceApproved.value = '0';
            }
            return;
        }

        if (!differenceReason || differenceReason.value.trim() === '') {
            event.preventDefault();
            if (closeAlert) {
                closeAlert.classList.remove('hidden');
                closeAlert.textContent = 'Debes ingresar un motivo porque el cierre no cuadra.';
            }
            return;
        }

        if (!canApprove) {
            event.preventDefault();
            if (closeAlert) {
                closeAlert.classList.remove('hidden');
                closeAlert.textContent = 'Solo Admin puede confirmar cierre con diferencia.';
            }
            return;
        }

        if (differenceApproved && differenceApproved.value !== '1') {
            event.preventDefault();
            differenceApprovalError?.classList.add('hidden');
            if (differenceApprovalPassword) {
                differenceApprovalPassword.value = '';
            }
            openModal(differenceApprovalModal);
        }
    });

    confirmCloseWithDiff?.addEventListener('click', () => {
        if (!differenceApprovalPassword || differenceApprovalPassword.value.trim() === '') {
            differenceApprovalError?.classList.remove('hidden');
            return;
        }

        if (differenceApproved) {
            differenceApproved.value = '1';
        }

        const oldHidden = closeForm?.querySelector('input[name="supervisor_password"]');
        if (oldHidden) {
            oldHidden.value = differenceApprovalPassword.value;
        } else if (closeForm) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'supervisor_password';
            hidden.value = differenceApprovalPassword.value;
            closeForm.appendChild(hidden);
        }

        closeModal(differenceApprovalModal);
        closeForm?.submit();
    });

    document.querySelectorAll('[data-close-difference-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            closeModal(differenceApprovalModal);
        });
    });

    document.querySelectorAll('.js-open-void-modal').forEach((button) => {
        button.addEventListener('click', () => {
            if (!voidForm || !voidRouteTemplate) {
                return;
            }

            const movementId = button.getAttribute('data-movement-id') || '';
            const movementLabel = button.getAttribute('data-movement-label') || '-';
            voidForm.action = voidRouteTemplate.replace('__MOVEMENT__', movementId);
            if (voidLabel) {
                voidLabel.textContent = movementLabel;
            }

            const reason = document.getElementById('void-reason');
            if (reason) {
                reason.value = '';
            }

            openModal(voidModal);
        });
    });

    document.querySelectorAll('[data-close-void-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            closeModal(voidModal);
        });
    });

    openMonthlyMovementsModalButton?.addEventListener('click', () => {
        openModal(monthlyMovementsModal);
    });

    document.querySelectorAll('[data-close-monthly-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            closeModal(monthlyMovementsModal);
        });
    });

    monthlyMovementsModal?.addEventListener('click', (event) => {
        if (event.target === monthlyMovementsModal) {
            closeModal(monthlyMovementsModal);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && monthlyMovementsModal && !monthlyMovementsModal.classList.contains('hidden')) {
            closeModal(monthlyMovementsModal);
        }
    });
}
