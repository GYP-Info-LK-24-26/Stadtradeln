/**
 * Dashboard: Tagesdialog zum Anzeigen, Hinzufügen, Bearbeiten und Löschen von Touren.
 * Daten kommen als JSON aus #dashboardData (siehe templates/pages/dashboard.php).
 */
(function () {
    'use strict';

    var data = JSON.parse(document.getElementById('dashboardData').textContent);
    var dialog = document.getElementById('dayDialog');
    var list = document.getElementById('dayTourList');
    var template = document.getElementById('tourItemTemplate');
    var form = document.getElementById('tourForm');
    var distance = document.getElementById('formDistance');
    var submit = document.getElementById('formSubmit');
    var cancelEdit = document.getElementById('formCancelEdit');
    var errorBox = document.getElementById('dayDialogError');

    var kmFormat = new Intl.NumberFormat('de-DE', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
    var dateFormat = new Intl.DateTimeFormat('de-DE', { weekday: 'long', day: 'numeric', month: 'long' });

    function parseDate(iso) {
        var p = iso.split('-');
        return new Date(+p[0], +p[1] - 1, +p[2]);
    }

    function renderList(date) {
        var tours = data.toursByDate[date] || [];
        list.innerHTML = '';
        var total = 0;

        tours.forEach(function (tour, i) {
            total += Number(tour.distance);
            var item = template.content.firstElementChild.cloneNode(true);
            item.dataset.id = tour.id;
            item.style.animationDelay = (i * 40) + 'ms';
            item.querySelector('.tour-dist').textContent = kmFormat.format(tour.distance) + ' km';
            item.querySelector('.btn-edit').addEventListener('click', function () { startEdit(tour, item); });
            item.querySelector('.btn-delete').addEventListener('click', function () { deleteTour(tour.id); });
            list.appendChild(item);
        });

        document.getElementById('dayDialogSubtitle').textContent = tours.length
            ? tours.length + (tours.length === 1 ? ' Tour' : ' Touren') + ' · ' + kmFormat.format(total) + ' km'
            : 'Noch keine Touren an diesem Tag';
    }

    function setMode(editing) {
        submit.querySelector('span').textContent = editing ? 'Speichern' : 'Hinzufügen';
        document.getElementById('formDistanceLabel').textContent = editing ? 'Distanz bearbeiten' : 'Neue Tour';
        cancelEdit.hidden = !editing;
        form.action = editing ? '/dashboard/tour/update' : '/dashboard/tour';
    }

    function resetForm() {
        document.getElementById('formTourId').value = '';
        distance.value = '';
        list.querySelectorAll('.is-editing').forEach(function (el) { el.classList.remove('is-editing'); });
        setMode(false);
    }

    function startEdit(tour, item) {
        resetForm();
        item.classList.add('is-editing');
        document.getElementById('formTourId').value = tour.id;
        distance.value = tour.distance;
        setMode(true);
        distance.focus();
        distance.select();
    }

    function deleteTour(id) {
        var deleteForm = document.getElementById('deleteForm');
        document.getElementById('deleteTourId').value = id;
        deleteForm.requestSubmit();
    }

    function openDay(date, error) {
        if (!date) return;
        document.getElementById('formDate').value = date;
        document.getElementById('dayDialogTitle').textContent = dateFormat.format(parseDate(date));
        errorBox.hidden = !error;
        errorBox.querySelector('span').textContent = error || '';
        renderList(date);
        resetForm();
        App.openDialog(dialog);
        // Auf Touch-Geräten nicht automatisch die Tastatur öffnen
        if (window.matchMedia('(hover: hover)').matches) distance.focus();
    }

    document.querySelectorAll('[data-open-day]').forEach(function (el) {
        el.addEventListener('click', function () { openDay(el.dataset.openDay); });
    });

    cancelEdit.addEventListener('click', resetForm);

    form.addEventListener('submit', function () {
        submit.disabled = true;
    });

    // Nach serverseitigem Validierungsfehler den betroffenen Tag wieder öffnen
    if (data.reopenDate) {
        openDay(data.reopenDate, data.error);
    }
}());
