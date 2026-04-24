/**
 * Password strength meter using zxcvbn.
 * Requires zxcvbn to be loaded before this script.
 *
 * The meter HTML must already exist in the DOM as:
 *   <div class="pw-strength" id="pw-strength-{inputId}">
 *     <div class="pw-strength-bar">
 *       <div class="pw-strength-seg"></div> × 4
 *     </div>
 *     <span class="pw-strength-label"></span>
 *   </div>
 *
 * Styling is driven entirely by a data-score="0..4" attribute — no inline
 * styles — so the meter integrates with the CSS theme.
 *
 * Form submission is blocked via setCustomValidity() when score < 2.
 */
(function () {
    'use strict';

    var LABELS = ['Sehr schwach', 'Schwach', 'Mäßig', 'Stark', 'Sehr stark'];
    var MIN_SCORE = 2;

    window.initPasswordStrength = function (inputId, contextFieldIds) {
        var input = document.getElementById(inputId);
        var meter = document.getElementById('pw-strength-' + inputId);
        if (!input || !meter) return;
        if (typeof zxcvbn === 'undefined') {
            console.warn('password-strength.js: zxcvbn nicht geladen.');
            return;
        }

        var label = meter.querySelector('.pw-strength-label');

        function reset() {
            delete meter.dataset.score;
            if (label) label.textContent = '';
            input.setCustomValidity('');
        }

        input.addEventListener('input', function () {
            var val = this.value;
            if (!val) { reset(); return; }

            var context = (contextFieldIds || [])
                .map(function (id) { return (document.getElementById(id) || {}).value || ''; })
                .filter(Boolean);

            var score = zxcvbn(val, context).score;

            meter.dataset.score = score;
            if (label) label.textContent = LABELS[score];

            input.setCustomValidity(
                score < MIN_SCORE
                    ? 'Passwort ist zu schwach – bitte wähle mindestens ein "Mäßiges" Passwort.'
                    : ''
            );
        });
    };
}());
