<?php

namespace App;

enum GameStage: string
{
    case START = 'start';
    case BEFORE_CAFE_GIFT = 'before_cafe_gift';
    case CAKE_GIFT = "cake_gift";
    case BEFORE_WEIRD_FRUIT = 'before_weird_fruit';
    case WEIRD_FRUIT = 'weird_fruit';
    case BEFORE_RIVER_BOTTLE = 'before_river_bottle';
    case RIVER_BOTTLE = 'river_bottle';
    case BEFORE_LAKE = 'before_lake';
    case LAKE = 'lake';
    case BEFORE_FORT = 'before_fort';
    case FORT = 'fort';
    case AFTER_FORT = 'after_fort';
    case BEFORE_TREASURE = 'before_treasure';
    case TREASURE = 'treasure';
    case FINISH = 'finish';

    public function playerContext(): string
    {
        return match ($this) {
            self::START => "Gracz pierwszy raz uruchomił aplikację gry i nie wie, co ma robić. Celem gracza jest uzyskanie dostępu do lokalizacji pierwszego fragmentu mapy i zapoznanie się z zasadami gry.",
            self::BEFORE_CAFE_GIFT => "Zadaniem gracza jest udanie się do piekarni 'U Braci' niedaleko miejsca zamieszkania na Prądniku Czerwonym. Tam na hasło 'zielony ork' zostanie jej wydana skrzynka.",
            self::CAKE_GIFT => "Zadaniem gracza jest znalezienie lokalizacji z mapy w świecie rzeczywistym oraz zjedzenie ciasta w lokalu.",
            self::BEFORE_WEIRD_FRUIT => "Zadaniem gracza jest udanie się do znalezionej wcześniej lokalizacji w Parku Lotników.",
            self::WEIRD_FRUIT => "Zadaniem gracza jest odnalezienie ukrytego drugiego fragmentu mapy w Parku Lotników.",
            self::BEFORE_RIVER_BOTTLE => "Zadaniem gracza jest wyznaczenie lokalizacji na mapie i udanie się do niej.",
            self::RIVER_BOTTLE => 'Zadaniem gracza jest odnalezienie ukrytego trzeciego fragmentu mapy w okolicy lokalizacji.',
            self::BEFORE_LAKE => "Zadaniem gracza jest wyznaczenie lokalizacji z trzeciego fragmentu mapy.",
            self::LAKE => "Zadaniem gracza jest odnalezienie czwartego fragmentu mapy. W tym celu osoba towarzysząca ma jej przygotować radar analogowy, który będzie wskazywał kierunek.",
            self::BEFORE_FORT => "Zadaniem gracza jest wyznaczenie lokalizacji z czwartego fragmentu mapy.",
            self::FORT => "Zadaniem gracza jest odnalezienie pięciu małych, plastikowych grzybków na terenie całego fortu.",
            self::AFTER_FORT => "Zadaniem gracza jest odnalezienie piątego fragmentu mapy, który znajduje się w 50.010698, 19.996919. Jest on ukryty obok drzewa od strony wschodniej.",
            self::BEFORE_TREASURE => "Zadaniem gracza jest wyznaczenie lokalizacji finałowego skarbu.",
            self::TREASURE => "Zadaniem gracza jest odnalezienie finałowego skarbu. Lokalizacja jest oznaczona na miejscu.",
            self::FINISH => "Gracz ukończył grę."
        };
    }

    public function guideContext(): string
    {
        return match ($this) {
            self::START => "Gracz właśnie uruchomił aplikację po raz pierwszy. Przywitaj go w charakterze cynicznego orka i zbuduj atmosferę. Nie zdradzaj szczegółów gry od razu - poczekaj, aż gracz potwierdzi gotowość.",
            self::BEFORE_CAFE_GIFT => "Obsługa lokalu jest poinstruowana, aby na hasło 'zielony ork' wydać skrzynkę z fragmentem mapy, który prowadzi do pierwszego punktu. W bonusie gracz dostaje również tiramisu.",
            self::CAKE_GIFT => "Pierwszy fragment mapy zawiera zaznaczoną bez żadnych wskazówek lokalizację 50.070267804061274, 19.991347616776793 - jest to na południu Parku Lotników, konkretnie drzewo.",
            self::BEFORE_WEIRD_FRUIT => "Użytkownik na miejscu powinien się znajdować na południu Parku Lotników przy wyjściu na al. Pokoju przy skrzyżowaniu przed galerią M1. Tę informację podaj jako wskazówkę graczowi.",
            self::WEIRD_FRUIT => "Drugi fragment mapy jest przyklejony do sztucznego, drewnianego jabłka zawieszonego na gałęzi drzewa w zagajniku na lewo od wyjścia z parku patrząc na M1. Dokładna lokalizacja to .",
            self::BEFORE_RIVER_BOTTLE => "Drugi fragment mapy zawiera zaznaczoną lokalizację 50.060146, 19.985685 - jest to zakole Wisły przy ujściu Bałuchy obok mostu Ofiar Dąbia. Użytkownik na miejscu powinien się znajdywać przy brzegu rzeki obok ujścia Bałuchy. Trzeba zjechać ze ścieżki rowerowej.",
            self::RIVER_BOTTLE => 'Trzeci fragment mapy jest w plastikowej butelce, która jest zrzucona na sznurku jutowym do rzeki, a sznurek jest przywiązany do trzciny przy brzegu.',
            self::BEFORE_LAKE => "Trzeci fragment mapy wskazuje na połudnowią część zalewu Bagry. Dokładne współrzędne to 50.030933, 19.991805.",
            self::LAKE => "Czwarty fragment mapy znajduje się w środku pływającej butelki plastikowej w wodzie zalewu w okolicach południowego brzegu w trzcinie cukrowej. Bojka unosi się na wodzie. W celu jej odnalezienia gracz musi wynająć rowerek wodny albo kajak i fizycznie podpłynąć w to miejsce.",
            self::BEFORE_FORT => "Czwarty fragment mapy wskazuje na fort 50 na Prokocimiu. Współrzędne to 50.010242305940196, 19.997182737475455.",
            self::FORT => "Grzyby są rozrzucone po terenie całego fortu. Po odnalezieniu wszystkich gracz otrzyma lokalizację piątego fragmentu mapy.",
            self::AFTER_FORT => "Tutaj lokalizację podajesz ty w nagrodę za odnalezienie grzybków z poprzedniego etapu. Piąty fragment mapy znajduje się w 50.009961, 19.995116 (ukryty nisko na drzewie przy wyjściu na ul. Mokrą).",
            self::BEFORE_TREASURE => "Lokalizacja na mapie to 50.001718, 20.017367. Wskazuje na północną granicę Lasu Krzyszkowickiego.",
            self::TREASURE => "Skarb jest zakopany pod ziemią, a w ziemię and nim jest wbity sztuczny muchomor czerwony. Skarbem jest kuferek, który w środku ma prezent oraz skarb. Jadąc główną utwardzoną drogą na zachód w lesie należy ok. 50 m przed wyjazdem zjechać w lewo z drogi i wejść w głąb lasu na ok. 10 metrów.",
            self::FINISH => "Pożegnaj się z graczem w cyniczny sposób i pogratuluj zwycięstwa."
        };
    }

    public function completionCriteria(): string
    {
        return match ($this) {
            self::START => "Gracz musi ci w dowolny sposób potwierdzić, że jest gotowy na rozpoczęcie gry. Zweryfikuj odpowiedź narzędziem.",
            self::BEFORE_CAFE_GIFT => "Gracz musi podać trzycyfrowy kod znajdujący się na rewersie fragmentu mapy.",
            self::CAKE_GIFT => "Gracz musi słownie potwierdzić lokalizację w przybliżeniu. Nie musi podawać dokładnych współrzędnych.",
            self::BEFORE_WEIRD_FRUIT => "Gracz musi potwierdzić, że jest na miejscu wyznaczonym na mapie.",
            self::WEIRD_FRUIT => "Gracz musi podać trzycyfrowy kod znajdujący się na rewersie fragmentu mapy.",
            self::BEFORE_RIVER_BOTTLE => "Gracz musi słownie potwierdzić, że znajduje się w lokalizacji.",
            self::RIVER_BOTTLE => 'Gracz musi podać trzycyfrowy kod znajdujący się na rewersie fragmentu mapy.',
            self::BEFORE_LAKE => 'Gracz musi potwierdzić, że wyznaczył lokalizację trzeciego fragmentu mapy i wie, gdzie ma iść.',
            self::LAKE => "Gracz musi podać trzycyfrowy kod znajdujący się na rewersie fragmentu mapy.",
            self::BEFORE_FORT => 'Gracz musi potwierdzić, że wyznaczył lokalizację czwartego fragmentu mapy i wie, gdzie ma iść.',
            self::FORT => "Tutaj gracz po odnalezieniu wszystkich pięciu grzybków ma tobie ten fakt zameldować. Po wywołaniu narzędzia CompleteStage etap zostanie zaliczony bezwarunkowo.",
            self::AFTER_FORT => "Gracz musi podać trzycyfrowy kod znajdujący się na rewersie fragmentu mapy.",
            self::BEFORE_TREASURE => 'Gracz musi potwierdzić, że wyznaczył lokalizację skarbu i wie, gdzie ma iść.',
            self::TREASURE => "Gracz musi potwierdzić, że wykopał skarb oraz podać zawartość. Jest to voucher na zjazd tyrolką w Trzebini oraz sztuczne monety czekoladowe.",
            self::FINISH => "Nie ma już dalszych etapów."
        };
    }

    public function next(): ?self
    {
        return match ($this) {
            self::START => self::BEFORE_CAFE_GIFT,
            self::BEFORE_CAFE_GIFT => self::CAKE_GIFT,
            self::CAKE_GIFT => self::BEFORE_WEIRD_FRUIT,
            self::BEFORE_WEIRD_FRUIT => self::WEIRD_FRUIT,
            self::WEIRD_FRUIT => self::BEFORE_RIVER_BOTTLE,
            self::BEFORE_RIVER_BOTTLE => self::RIVER_BOTTLE,
            self::RIVER_BOTTLE => self::BEFORE_LAKE,
            self::BEFORE_LAKE => self::LAKE,
            self::LAKE => self::BEFORE_FORT,
            self::BEFORE_FORT => self::FORT,
            self::FORT => self::AFTER_FORT,
            self::AFTER_FORT => self::BEFORE_TREASURE,
            self::BEFORE_TREASURE => self::TREASURE,
            self::TREASURE => self::FINISH,
            self::FINISH => null
        };
    }

    public function answer(): string|null
    {
        return match ($this) {
            self::START => null,
            self::BEFORE_CAFE_GIFT => '134',
            self::CAKE_GIFT => null,
            self::BEFORE_WEIRD_FRUIT => null,
            self::WEIRD_FRUIT => '229',
            self::BEFORE_RIVER_BOTTLE => null,
            self::RIVER_BOTTLE => '417',
            self::BEFORE_LAKE => null,
            self::LAKE => '934',
            self::BEFORE_FORT => null,
            self::FORT => null,
            self::AFTER_FORT => '879',
            self::BEFORE_TREASURE => null,
            self::TREASURE => null,
            self::FINISH => null
        };
    }
}
