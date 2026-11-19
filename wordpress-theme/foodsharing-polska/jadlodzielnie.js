/* Mapa jadłodzielni Foodsharing Polska (Leaflet + OpenStreetMap). */
(function () {
  'use strict';
  var el = document.getElementById('fs-map');
  if (!el) return;
  var items = Array.prototype.slice.call(document.querySelectorAll('.fs-place'));
  var cities = Array.prototype.slice.call(document.querySelectorAll('.fs-cities .district'));
  var search = document.getElementById('fs-search');
  var count = document.getElementById('fs-count');
  var near = document.getElementById('fs-near');
  var onlyCity = el.dataset.city || '';
  var unitOne = count && count.dataset.unitOne || 'miejscowości', unitMany = count && count.dataset.unitMany || 'miejscowościach';
  var typeBoxes = Array.prototype.slice.call(document.querySelectorAll('.fs-type-filter input'));
  var map = null, cluster = null, markers = {}, data = {}, userMarker = null, started = false, pending = null;

  function fold(s) {
    return s.toLowerCase().normalize('NFKD').replace(/[̀-ͯ]/g, '').replace(/ł/g, 'l');
  }
  function plural(n, one, few, many) {
    if (n === 1) return one;
    return (n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 12 || n % 100 > 14)) ? few : many;
  }
  function node(tag, cls, text) {
    var e = document.createElement(tag);
    if (cls) e.className = cls;
    if (text) e.textContent = text;
    return e;
  }
  function popup(p) {
    var box = node('div', 'fs-popup');
    if (p.img) {
      var img = node('img', 'fs-popup-photo');
      img.src = p.img; img.alt = 'Jadłodzielnia: ' + p.c + ', ' + p.n; img.loading = 'lazy';
      if (p.imgw && p.imgh) { img.width = p.imgw; img.height = p.imgh; }
      if (p.full) {
        var big = node('a', 'fs-popup-photo-link'); big.href = p.full; big.target = '_blank'; big.rel = 'noopener';
        big.setAttribute('aria-label', 'Powiększ zdjęcie'); big.appendChild(img); box.appendChild(big);
      } else box.appendChild(img);
    }
    box.appendChild(node('span', 'fs-popup-city', p.c + (p.r ? ' · ' + p.r : '')));
    box.appendChild(node('strong', '', p.n));
    if (p.ad && p.ad !== p.n) box.appendChild(node('span', 'fs-popup-addr', p.ad));
    box.appendChild(node('span', 'fs-popup-hours', p.h ? '◷ ' + p.h : 'Godziny: brak danych'));
    if (p.d) box.appendChild(node('p', '', p.d));
    var links = node('div', 'fs-popup-links');
    var route = node('a', '', 'Trasa ↗');
    route.href = 'https://www.google.com/maps/dir/?api=1&destination=' + p.a + ',' + p.o;
    route.target = '_blank'; route.rel = 'noopener noreferrer';
    links.appendChild(route);
    (p.l || []).forEach(function (url) {
      if (!/^https?:\/\//.test(url)) return;
      var a = node('a', '', url.indexOf('facebook') > -1 ? 'Facebook ↗' : 'Strona ↗');
      a.href = url; a.target = '_blank'; a.rel = 'noopener noreferrer';
      links.appendChild(a);
    });
    box.appendChild(links);
    return box;
  }
  function icon(t) {
    return L.divIcon({ className: 'fs-pin fs-pin-' + t, html: '<span></span>', iconSize: [26, 34], iconAnchor: [13, 33], popupAnchor: [0, -30] });
  }

  function visibleIds() {
    var q = fold(search ? search.value.trim() : '');
    var types = {};
    typeBoxes.forEach(function (b) { types[b.value] = b.checked; });
    var ids = [];
    items.forEach(function (li) {
      var ok = types[li.dataset.type] !== false && (!q || li.dataset.search.indexOf(q) > -1);
      li.hidden = !ok;
      if (ok) ids.push(li.dataset.id);
    });
    cities.forEach(function (d) {
      var any = d.querySelector('.fs-place:not([hidden])');
      d.hidden = !any;
      if (q) d.open = !!any;
    });
    var cityCount = cities.filter(function (d) { return !d.hidden; }).length;
    if (count) count.textContent = ids.length + ' ' + plural(ids.length, 'punkt', 'punkty', 'punktów') + ' w ' + cityCount + ' ' + plural(cityCount, unitOne, unitMany, unitMany);
    return { ids: ids, q: q };
  }

  function apply(fit) {
    var v = visibleIds();
    if (!map) return;
    cluster.clearLayers();
    var layers = v.ids.map(function (id) { return markers[id]; }).filter(Boolean);
    cluster.addLayers(layers);
    if (fit && v.q && layers.length) {
      map.fitBounds(L.latLngBounds(layers.map(function (m) { return m.getLatLng(); })).pad(0.2), { maxZoom: 15 });
    }
  }

  function showOnMap(id) {
    var m = markers[id];
    if (!m) { pending = id; return; }
    if (!cluster.hasLayer(m)) {
      if (search) search.value = '';
      typeBoxes.forEach(function (b) { b.checked = true; });
      apply(false);
    }
    el.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'center' });
    cluster.zoomToShowLayer(m, function () { m.openPopup(); });
  }

  function distance(a1, o1, a2, o2) {
    var r = Math.PI / 180, dA = (a2 - a1) * r, dO = (o2 - o1) * r;
    var h = Math.sin(dA / 2) * Math.sin(dA / 2) + Math.cos(a1 * r) * Math.cos(a2 * r) * Math.sin(dO / 2) * Math.sin(dO / 2);
    return 12742 * Math.asin(Math.sqrt(h));
  }

  function findNear() {
    var status = document.getElementById('fs-near-status');
    if (!navigator.geolocation) { status.textContent = 'Twoja przeglądarka nie udostępnia lokalizacji.'; return; }
    status.textContent = 'Ustalam Twoją lokalizację…';
    navigator.geolocation.getCurrentPosition(function (pos) {
      var la = pos.coords.latitude, lo = pos.coords.longitude;
      var ids = visibleIds().ids;
      var list = ids.map(function (id) { var p = data[id]; return { p: p, km: distance(la, lo, p.a, p.o) }; })
        .sort(function (a, b) { return a.km - b.km; }).slice(0, 3);
      if (!list.length) { status.textContent = 'Brak punktów dla wybranych filtrów.'; return; }
      if (userMarker) userMarker.remove();
      userMarker = L.circleMarker([la, lo], { radius: 8, color: '#342b20', weight: 3, fillColor: '#ffffff', fillOpacity: 1 }).addTo(map).bindTooltip('Twoja lokalizacja');
      var pts = list.map(function (x) { return [x.p.a, x.p.o]; }).concat([[la, lo]]);
      map.fitBounds(L.latLngBounds(pts).pad(0.25), { maxZoom: 15 });
      var first = list[0];
      status.textContent = 'Najbliżej: ' + first.p.c + ', ' + first.p.n + ' (' + first.km.toFixed(1).replace('.', ',') + ' km).';
      el.scrollIntoView({ block: 'center' });
    }, function () {
      status.textContent = 'Nie udało się ustalić lokalizacji. Wpisz miasto w wyszukiwarkę.';
    }, { enableHighAccuracy: false, timeout: 10000, maximumAge: 600000 });
  }

  function start() {
    if (started || typeof L === 'undefined') return;
    started = true;
    fetch(el.dataset.src).then(function (r) { return r.json(); }).then(function (rows) {
      map = L.map(el, { scrollWheelZoom: false, zoomSnap: 0.5 }).setView([52.1, 19.3], window.innerWidth < 700 ? 5.5 : 6);
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>'
      }).addTo(map);
      cluster = L.markerClusterGroup({
        showCoverageOnHover: false, maxClusterRadius: 44, spiderfyOnMaxZoom: true,
        iconCreateFunction: function (c) {
          var n = c.getChildCount();
          return L.divIcon({ html: '<span>' + n + '</span>', className: 'fs-cluster' + (n > 9 ? ' fs-cluster-big' : ''), iconSize: [40, 40] });
        }
      });
      if (onlyCity) rows = rows.filter(function (p) { return p.c === onlyCity; });
      rows.forEach(function (p) {
        data[p.id] = p;
        var m = L.marker([p.a, p.o], { icon: icon(p.t), title: p.c + ' – ' + p.n, alt: p.c + ' – ' + p.n, keyboard: true });
        m.bindPopup(function () { return popup(p); }, { maxWidth: 290, minWidth: 240, autoPanPadding: [20, 20] });
        markers[p.id] = m;
      });
      map.addLayer(cluster);
      apply(false);
      if (onlyCity && rows.length) map.fitBounds(L.latLngBounds(rows.map(function (p) { return [p.a, p.o]; })).pad(0.05));
      map.once('focus click', function () { map.scrollWheelZoom.enable(); });
      el.classList.add('is-ready');
      if (pending !== null) { showOnMap(pending); pending = null; }
    }).catch(function () {
      el.classList.add('is-error');
      el.textContent = 'Nie udało się wczytać mapy. Skorzystaj z listy poniżej.';
    });
  }

  if (search) search.addEventListener('input', function () { apply(true); });
  typeBoxes.forEach(function (b) { b.addEventListener('change', function () { apply(false); }); });
  document.addEventListener('click', function (e) {
    var b = e.target.closest && e.target.closest('.fs-show');
    if (b) { start(); showOnMap(b.dataset.id); }
  });
  if (near) near.addEventListener('click', function () { start(); var wait = setInterval(function () { if (map) { clearInterval(wait); findNear(); } }, 100); });
  visibleIds();
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (en) { if (en[0].isIntersecting) { io.disconnect(); start(); } }, { rootMargin: '300px' });
    io.observe(el);
  } else {
    start();
  }
})();
