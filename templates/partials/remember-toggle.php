<?php
/** Schalter „Angemeldet bleiben“. Variable: $remember (bool, vorausgewählt) */
?>
<label class="switch">
    <input type="checkbox" role="switch" name="remember" value="1"<?= !empty($remember) ? ' checked' : '' ?>>
    <span class="switch-track" aria-hidden="true"></span>
    <span class="switch-label">Angemeldet bleiben</span>
</label>
