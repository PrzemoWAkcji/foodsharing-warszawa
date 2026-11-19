# Motyw WordPress Foodsharing Polska

Gotowa paczka: `foodsharing-polska.zip`. Źródła motywu: `foodsharing-polska/`.
Statyczny podgląd projektu pozostaje w katalogu głównym repozytorium i na GitHub Pages.

## Co zawiera motyw

- Stronę główną z obecnym układem i trzema najnowszymi wpisami z kategorii WordPress `aktualnosci` (regulamin jest poza tą kategorią). Karty korzystają z obrazów wyróżniających; dla trzech wpisów obecnych w podglądzie dostępne są lokalne obrazy awaryjne.
- Szablon strony o slugu `warszawa`: spis 46 jadłodzielni, wyszukiwarkę adresów, link do miejskiej mapy i zasady dzielenia.
- Widoki zwykłych stron, wpisów, listy aktualności pod istniejącym adresem `/aktualnosci/` i strony 404.
- Oficjalne logo, obrazy z artykułów i lokalnie hostowane czcionki z obsługą polskich znaków.

## Po uzyskaniu dostępu przez Novamira

1. Zrób odczyt istniejącej witryny: motyw, wtyczki SEO, struktura stron, wpisów i menu, obecne adresy URL oraz kopia zapasowa.
2. Sprawdź aktualny regulamin, adresy i godziny jadłodzielni oraz obraz wyróżniający każdego wpisu. Spis w motywie pochodzi z Warszawa 19115 (22.10.2026). Do czasu tej kontroli motyw jest wersją do oceny.
3. Wgraj ZIP w **Wygląd → Motywy → Dodaj nowy → Wyślij motyw na serwer**, lecz aktywuj dopiero po podglądzie i akceptacji. Motyw nie wymaga dodatkowych wtyczek ani Elementora.
4. Ustaw stronę główną i utwórz opublikowaną stronę o slugu `warszawa` — WordPress sam zastosuje `page-warszawa.php`. Opcjonalnie utwórz stronę `Aktualności` i ustaw ją jako stronę wpisów w **Ustawienia → Czytanie**.
5. Sprawdź permalinki, menu, wtyczkę SEO i ewentualne przekierowania starych adresów. Nie dubluj meta tagów generowanych przez wtyczkę SEO.
6. Sprawdź podgląd na telefonie i komputerze, dostępność, Lighthouse i działanie wyszukiwarki jadłodzielni. Dopiero potem aktywuj motyw na produkcji.

Treść sekcji strony głównej i warszawskiej jest na razie zapisana w szablonach PHP; wpisy aktualności są edytowane w standardowym panelu WordPress. Dalsze przenoszenie sekcji do edytora warto wykonać po poznaniu struktury istniejącej witryny przez Novamira.
