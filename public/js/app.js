/**
 * GYP-Radeln – gemeinsames Verhalten für alle Seiten.
 *
 *  - [data-menu]              Dropdown-Menü (Account)
 *  - [data-dialog-open="id"]  öffnet <dialog id="id">; [data-dialog-close] schließt
 *  - form[data-confirm]       Bestätigungsdialog vor dem Absenden
 *  - [data-count-to]          zählt Zahlen beim Laden hoch
 *  - [data-inline-edit]       Inline-Bearbeitung eines Namens (Formular)
 *  - [data-password-toggle]   zeigt das Passwortfeld davor vorübergehend im Klartext
 */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Dialoge ---------- */

    function openDialog(dialog) {
        if (!dialog || dialog.open) return;
        dialog.classList.remove('is-closing');
        dialog.showModal();
    }

    function closeDialog(dialog) {
        if (!dialog || !dialog.open || dialog.classList.contains('is-closing')) return;
        if (reduceMotion) { dialog.close(); return; }
        dialog.classList.add('is-closing');
        dialog.addEventListener('animationend', function done() {
            dialog.removeEventListener('animationend', done);
            dialog.classList.remove('is-closing');
            dialog.close();
        });
    }

    window.App = { openDialog: openDialog, closeDialog: closeDialog };

    document.addEventListener('click', function (e) {
        var opener = e.target.closest('[data-dialog-open]');
        if (opener) {
            openDialog(document.getElementById(opener.dataset.dialogOpen));
            return;
        }
        var closer = e.target.closest('[data-dialog-close]');
        if (closer) {
            closeDialog(closer.closest('dialog'));
            return;
        }
        // Klick auf den Hintergrund schließt den Dialog
        if (e.target.tagName === 'DIALOG' && e.target.open) {
            var r = e.target.getBoundingClientRect();
            var inside = e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom;
            if (!inside) closeDialog(e.target);
        }
    });

    // Escape mit Schließanimation statt sofortigem Schließen
    document.addEventListener('cancel', function (e) {
        if (e.target.tagName === 'DIALOG') {
            e.preventDefault();
            closeDialog(e.target);
        }
    }, true);

    document.querySelectorAll('dialog[data-open-on-load]').forEach(openDialog);

    /* ---------- Bestätigungen ---------- */

    var confirmDialog = document.getElementById('confirmDialog');
    var pendingForm = null;

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form.dataset.confirm || !confirmDialog || form.dataset.confirmed) return;
        e.preventDefault();
        pendingForm = form;

        var danger = form.dataset.confirmVariant === 'danger';
        document.getElementById('confirmTitle').textContent = form.dataset.confirmTitle || 'Bist du sicher?';
        document.getElementById('confirmText').textContent = form.dataset.confirm;
        document.getElementById('confirmIcon').classList.toggle('is-danger', danger);
        var ok = document.getElementById('confirmOk');
        ok.textContent = form.dataset.confirmOk || 'Bestätigen';
        ok.className = 'btn ' + (danger ? 'btn-danger' : 'btn-primary');
        openDialog(confirmDialog);
        ok.focus();
    });

    if (confirmDialog) {
        document.getElementById('confirmOk').addEventListener('click', function () {
            if (!pendingForm) return;
            pendingForm.dataset.confirmed = '1';
            this.disabled = true;
            pendingForm.submit();
        });
        confirmDialog.addEventListener('close', function () {
            document.getElementById('confirmOk').disabled = false;
        });
    }

    /* ---------- Dropdown-Menü ---------- */

    document.querySelectorAll('[data-menu]').forEach(function (menu) {
        var toggle = menu.querySelector('[data-menu-toggle]');
        var items = function () { return menu.querySelectorAll('[role="menuitem"]'); };

        function setOpen(open) {
            menu.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        toggle.addEventListener('click', function () {
            setOpen(!menu.classList.contains('is-open'));
        });

        document.addEventListener('click', function (e) {
            if (!menu.contains(e.target)) setOpen(false);
        });

        menu.addEventListener('keydown', function (e) {
            var list = Array.prototype.slice.call(items());
            var idx = list.indexOf(document.activeElement);
            if (e.key === 'Escape') {
                setOpen(false);
                toggle.focus();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                setOpen(true);
                list[(idx + 1) % list.length].focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                setOpen(true);
                list[(idx - 1 + list.length) % list.length].focus();
            }
        });
    });

    /* ---------- Zahlen hochzählen ---------- */

    document.querySelectorAll('[data-count-to]').forEach(function (el) {
        var target = parseFloat(el.dataset.countTo) || 0;
        var decimals = parseInt(el.dataset.decimals || '0', 10);
        var fmt = new Intl.NumberFormat('de-DE', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
        if (reduceMotion || target === 0) { el.textContent = fmt.format(target); return; }

        var duration = 1100;
        var start = null;
        function step(ts) {
            if (start === null) start = ts;
            var t = Math.min(1, (ts - start) / duration);
            var eased = 1 - Math.pow(1 - t, 4);
            el.textContent = fmt.format(target * eased);
            if (t < 1) requestAnimationFrame(step);
        }
        el.textContent = fmt.format(0);
        requestAnimationFrame(step);
    });

    /* ---------- Inline-Bearbeitung ---------- */

    document.querySelectorAll('[data-inline-edit]').forEach(function (form) {
        var input = form.querySelector('.inline-edit-input');
        var text = form.querySelector('.inline-edit-text');
        var button = form.querySelector('.inline-edit-btn');
        var original = text.textContent.trim();

        // Breite des überlagerten Eingabefelds: etwas breiter als der Text,
        // aber nie über den Container oder daneben liegende Aktionen hinaus.
        function fitInput() {
            var box = form.getBoundingClientRect();
            var container = form.closest('.page-header, .card') || form.parentElement;
            var rect = container.getBoundingClientRect();
            var right = rect.right - parseFloat(getComputedStyle(container).paddingRight);
            var actions = container.querySelector('.page-actions');
            if (actions) {
                var a = actions.getBoundingClientRect();
                if (a.top < box.bottom && a.bottom > box.top) right = Math.min(right, a.left - 12);
            }
            var available = right - box.left + 9;
            input.style.width = Math.min(Math.max(text.offsetWidth + 56, 240), available) + 'px';
        }

        function start() {
            form.classList.add('is-editing');
            fitInput();
            input.focus();
            input.select();
        }

        function cancel() {
            input.value = original;
            form.classList.remove('is-editing');
        }

        function commit() {
            var value = input.value.trim();
            if (value && value !== original) {
                form.submit();
            } else {
                cancel();
            }
        }

        button.addEventListener('click', start);
        input.addEventListener('blur', commit);
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                input.removeEventListener('blur', commit);
                commit();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                input.removeEventListener('blur', commit);
                cancel();
                input.addEventListener('blur', commit);
            }
        });
    });

    /* ---------- Passwort anzeigen ---------- */

    function setPasswordVisible(button, visible) {
        var input = button.parentElement.querySelector('input');
        input.type = visible ? 'text' : 'password';
        button.setAttribute('aria-pressed', visible ? 'true' : 'false');
        button.setAttribute('aria-label', visible ? 'Passwort verbergen' : 'Passwort anzeigen');
    }

    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            setPasswordVisible(button, button.getAttribute('aria-pressed') !== 'true');
        });
        // Vor dem Absenden wieder maskieren, damit Passwortmanager das Feld erkennen
        // und das Passwort nach Zurück-Navigation nicht sichtbar bleibt
        var form = button.closest('form');
        if (form) form.addEventListener('submit', function () { setPasswordVisible(button, false); });
    });
}());
