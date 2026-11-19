const toggle = document.querySelector('.menu-toggle');
const nav = document.querySelector('.nav');
function closeMenu() { if (!toggle || !nav) return; toggle.setAttribute('aria-expanded', 'false'); nav.classList.remove('open'); }
toggle?.addEventListener('click', () => { const open = toggle.getAttribute('aria-expanded') !== 'true'; toggle.setAttribute('aria-expanded', String(open)); nav.classList.toggle('open', open); });
nav?.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
document.addEventListener('keydown', event => { if (event.key === 'Escape') closeMenu(); });

const placeSearch = document.querySelector('#place-search');
const placeCount = document.querySelector('#place-count');
const districts = [...document.querySelectorAll('.district')];

const normalize = value => value.toLocaleLowerCase('pl').normalize('NFD')
  .replace(/[\u0300-\u036f]/g, '').replace(/ł/g, 'l').trim();

placeSearch?.addEventListener('input', () => {
  const query = normalize(placeSearch.value);
  let visiblePlaces = 0;
  let visibleDistricts = 0;

  districts.forEach(district => {
    const districtMatches = normalize(district.dataset.district).includes(query);
    let matches = 0;
    district.querySelectorAll('.address-item').forEach(item => {
      const show = !query || districtMatches || normalize(item.dataset.search).includes(query);
      item.hidden = !show;
      if (show) matches++;
    });
    district.hidden = matches === 0;
    if (query) district.open = matches > 0;
    if (matches) visibleDistricts++;
    visiblePlaces += matches;
  });

  placeCount.textContent = visiblePlaces === 0
    ? 'Nie znaleziono punktów. Spróbuj innej nazwy ulicy lub dzielnicy.'
    : `${visiblePlaces} ${visiblePlaces === 1 ? 'punkt' : [2, 3, 4].includes(visiblePlaces % 10) && ![12, 13, 14].includes(visiblePlaces % 100) ? 'punkty' : 'punktów'} w ${visibleDistricts} ${visibleDistricts === 1 ? 'dzielnicy' : 'dzielnicach'}`;
});
