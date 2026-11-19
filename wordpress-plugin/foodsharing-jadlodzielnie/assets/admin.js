/* Mini-mapa w edycji jadłodzielni: klik/przeciągnięcie ustawia współrzędne, przycisk szuka po adresie. */
(function () {
  'use strict';
  var el = document.getElementById('fsj-map');
  if (!el || typeof L === 'undefined') return;
  var lat = document.getElementById('fsj_lat'), lng = document.getElementById('fsj_lng');
  var city = document.getElementById('fsj_city'), addr = document.getElementById('fsj_address');
  var status = document.getElementById('fsj-geo-status');
  var has = lat.value !== '' && lng.value !== '';
  var map = L.map(el).setView(has ? [+lat.value, +lng.value] : [52.1, 19.3], has ? 17 : 6);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
  var marker = null;
  function place(a, o, zoom) {
    a = Math.round(a * 1e6) / 1e6; o = Math.round(o * 1e6) / 1e6;
    lat.value = a; lng.value = o;
    if (!marker) {
      marker = L.marker([a, o], { draggable: true }).addTo(map);
      marker.on('dragend', function () { var p = marker.getLatLng(); place(p.lat, p.lng); });
    } else marker.setLatLng([a, o]);
    if (zoom) map.setView([a, o], zoom);
  }
  if (has) place(+lat.value, +lng.value);
  map.on('click', function (e) { place(e.latlng.lat, e.latlng.lng); });
  [lat, lng].forEach(function (i) { i.addEventListener('change', function () { if (lat.value && lng.value) place(+lat.value.replace(',', '.'), +lng.value.replace(',', '.'), 17); }); });
  // Ikona domyślna Leafleta wymaga obrazków – zastępujemy ją prostą pinezką.
  L.Marker.prototype.options.icon = L.divIcon({ className: '', html: '<div style="width:22px;height:22px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);background:#2b642c;border:3px solid #fff;box-shadow:0 2px 6px #0006"></div>', iconSize: [22, 22], iconAnchor: [11, 22] });
  if (marker) { var p = marker.getLatLng(); marker.remove(); marker = null; place(p.lat, p.lng); }
  document.getElementById('fsj-geocode').addEventListener('click', function () {
    var q = [addr.value.replace(/\(.*?\)/g, '').trim(), city.value.trim()].filter(Boolean).join(', ');
    if (!q) { status.textContent = 'Wpisz adres i miejscowość.'; return; }
    status.textContent = 'Szukam…';
    fetch('https://photon.komoot.io/api/?limit=1&lang=default&q=' + encodeURIComponent(q + ', Polska'))
      .then(function (r) { return r.json(); })
      .then(function (d) {
        var f = d.features && d.features[0];
        if (!f) { status.textContent = 'Nie znaleziono – kliknij miejsce na mapie.'; return; }
        place(f.geometry.coordinates[1], f.geometry.coordinates[0], 17);
        var pr = f.properties;
        status.textContent = 'Znaleziono: ' + [pr.street || pr.name, pr.housenumber, pr.city || pr.town || pr.village].filter(Boolean).join(' ') + '. Sprawdź i w razie potrzeby przesuń pinezkę.';
      })
      .catch(function () { status.textContent = 'Wyszukiwarka adresów nie odpowiada – kliknij miejsce na mapie.'; });
  });
  var today = document.getElementById('fsj-today');
  if (today) today.addEventListener('click', function () { document.getElementById('fsj_verified').value = new Date().toISOString().slice(0, 10); });
})();
