#!/usr/bin/env python3
"""Buduje mapę jadłodzielni z pliku data/jadlodzielnie.json.

Tworzy:
  - wordpress-theme/foodsharing-polska/assets/jadlodzielnie.json  (dane dla mapy)
  - wordpress-theme/foodsharing-polska/parts/jadlodzielnie-lista.html (lista HTML dla PHP)
  - jadlodzielnie/index.html + kopie zasobów w assets/ (statyczny podgląd GitHub Pages)

Uruchom: python3 tools/build_jadlodzielnie.py
"""
import html
import json
import shutil
import unicodedata
from pathlib import Path
from urllib.parse import quote

ROOT = Path(__file__).resolve().parent.parent
THEME = ROOT / 'wordpress-theme' / 'foodsharing-polska'
DATA = ROOT / 'data' / 'jadlodzielnie.json'
UPDATED = '19.11.2026'


def esc(s: str) -> str:
    return html.escape(s, quote=True)


def fold(s: str) -> str:
    s = unicodedata.normalize('NFKD', s.lower())
    return ''.join(c for c in s if not unicodedata.combining(c)).replace('ł', 'l')


def plural(n: int, one: str, few: str, many: str) -> str:
    if n == 1:
        return one
    if n % 10 in (2, 3, 4) and n % 100 not in (12, 13, 14):
        return few
    return many


def load():
    pts = json.loads(DATA.read_text(encoding='utf-8'))
    for i, p in enumerate(pts):
        p['id'] = i
    return pts


def route_url(p) -> str:
    return f"https://www.google.com/maps/dir/?api=1&destination={p['lat']},{p['lng']}"


def render_list(pts, warszawa_url: str) -> str:
    cities = {}
    for p in pts:
        cities.setdefault(p['city'], []).append(p)
    order = sorted(cities, key=lambda c: fold(c))
    out = ['<div class="districts fs-cities">']
    for city in order:
        items = cities[city]
        n = len(items)
        out.append(f'<details class="district" data-city="{esc(city)}"><summary><span>{esc(city)}</span>'
                   f'<span class="district-count">{n} {plural(n, "punkt", "punkty", "punktów")}</span>'
                   '<span class="district-chevron" aria-hidden="true">⌄</span></summary>')
        if city == 'Warszawa':
            out.append(f'<p class="fs-city-note">Pełny spis według dzielnic znajdziesz na <a href="{esc(warszawa_url)}">stronie Foodsharing Warszawa</a>.</p>')
        out.append('<ul class="fs-place-list">')
        for p in items:
            label = p['name'] + (f" ({p['district']})" if p.get('district') else '')
            search = fold(' '.join([city, p['name'], p.get('district', ''), p.get('desc', '')]))
            typ = 'Jadłodzielnia Foodsharing' if p['type'] == 'foodsharing' else 'Podobna inicjatywa'
            out.append(f'<li class="fs-place" data-id="{p["id"]}" data-type="{p["type"]}" data-search="{esc(search)}">'
                       f'<div><strong>{esc(label)}</strong><span class="fs-type fs-type-{p["type"]}">{typ}</span>'
                       + (f'<p>{esc(p["desc"])}</p>' if p.get('desc') else '') +
                       '</div><div class="fs-place-actions">'
                       f'<button type="button" class="fs-show" data-id="{p["id"]}">Na mapie</button>'
                       f'<a href="{esc(route_url(p))}" target="_blank" rel="noopener noreferrer" aria-label="Trasa: {esc(city + ", " + p["name"])}">Trasa ↗</a>'
                       '</div></li>')
        out.append('</ul></details>')
    out.append('</div>')
    return ''.join(out)


def map_json(pts) -> str:
    slim = [{
        'id': p['id'], 'c': p['city'], 'n': p['name'], 'd': p.get('desc', ''), 'r': p.get('district', ''),
        't': 'f' if p['type'] == 'foodsharing' else 'i', 'a': p['lat'], 'o': p['lng'],
        'l': p.get('links', []), 's': p['source'],
    } for p in pts]
    return json.dumps(slim, ensure_ascii=False, separators=(',', ':'))


def stats(pts):
    return len(pts), len({p['city'] for p in pts})


def build_static(pts, list_html: str):
    n, c = stats(pts)
    page = (ROOT / 'tools' / 'jadlodzielnie-static.html').read_text(encoding='utf-8')
    page = page.replace('{{LIST}}', list_html).replace('{{COUNT}}', str(n)).replace('{{CITIES}}', str(c)).replace('{{UPDATED}}', UPDATED)
    out = ROOT / 'jadlodzielnie' / 'index.html'
    out.parent.mkdir(exist_ok=True)
    out.write_text(page, encoding='utf-8')
    for name in ('jadlodzielnie.css', 'jadlodzielnie.js'):
        shutil.copy(THEME / name, ROOT / name)
    shutil.copy(THEME / 'assets' / 'jadlodzielnie.json', ROOT / 'assets' / 'jadlodzielnie.json')
    shutil.copytree(THEME / 'assets' / 'vendor', ROOT / 'assets' / 'vendor', dirs_exist_ok=True)


def main():
    pts = load()
    (THEME / 'assets' / 'jadlodzielnie.json').write_text(map_json(pts), encoding='utf-8')
    parts = THEME / 'parts'
    parts.mkdir(exist_ok=True)
    (parts / 'jadlodzielnie-lista.html').write_text(render_list(pts, '/warszawa/'), encoding='utf-8')
    n, c = stats(pts)
    (parts / 'jadlodzielnie-stats.json').write_text(json.dumps({'count': n, 'cities': c, 'updated': UPDATED}), encoding='utf-8')
    build_static(pts, render_list(pts, '../warszawa/'))
    print(f'{n} punktów, {c} miejscowości')


if __name__ == '__main__':
    main()
