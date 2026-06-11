<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$bledy = [];
$sukces = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $pola_tekstowe = [
        'imie' => 'Imię jest wymagane',
        'nazwisko' => 'Nazwisko jest wymagane',
        'data_urodzenia' => 'Data urodzenia jest wymagana',
        'pesel' => 'Numer PESEL jest wymagany',
        'email' => 'Adres e-mail jest wymagany',
        'kraj' => 'Kraj docelowy jest wymagany',
        'miejscowosc' => 'Miejscowość jest wymagana',
        'data_start' => 'Data rozpoczęcia jest wymagana',
        'data_koniec' => 'Data zakończenia jest wymagana',
        'dni' => 'Wybierz liczbę dni pobytu',
        'liczba_osob' => 'Podaj liczbę osób w pokoju'
    ];

    foreach ($pola_tekstowe as $klucz => $komunikat) {
        if (empty(trim($_POST[$klucz] ?? ''))) {
            $bledy[$klucz] = $komunikat;
        }
    }

    if (empty($bledy['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $bledy['email'] = "Podaj poprawny adres e-mail!";
    }

    if (empty($bledy['pesel']) && !preg_match('/^[0-9]{11}$/', $_POST['pesel'])) {
        $bledy['pesel'] = "PESEL must mieć dokładnie 11 cyfr!";
    }

    if (empty($bledy['liczba_osob']) && (int)$_POST['liczba_osob'] <= 0) {
        $bledy['liczba_osob'] = "Liczba osób musi być większa od 0!";
    }

    if (!isset($_POST['zgoda_rodo'])) {
        $bledy['zgoda_rodo'] = "Musisz zaakceptować zgodę RODO!";
    }

    if (empty($bledy)) {
        $znacznik_czasu = date('Y-m-d H:i:s');
        
        
        $tresc_osobowe = "[$znacznik_czasu] Osoba: {$_POST['imie']} {$_POST['nazwisko']} | PESEL: {$_POST['pesel']} | Ur: {$_POST['data_urodzenia']} | Tel: {$_POST['telefon']} | Email: {$_POST['email']} | Obywatelstwo: {$_POST['obywatelstwo']}\n";
        file_put_contents('dane_osobowe.txt', $tresc_osobowe, FILE_APPEND);
        
       
        $tresc_podrozy = "[$znacznik_czasu] Cel: {$_POST['kraj']} - {$_POST['miejscowosc']} | Termin: {$_POST['data_start']} do {$_POST['data_koniec']} ({$_POST['dni']} dni) | Typ: {$_POST['cel_podrozy']} | Hotel: {$_POST['rodzaj_zakwaterowania']} ({$_POST['standard_obiektu']}*) | Pokój: {$_POST['pref_pokoju']} (Osob: {$_POST['liczba_osob']})\n";
        file_put_contents('dane_podrozy.txt', $tresc_podrozy, FILE_APPEND);
        
        
        $status_ubezpieczenia = isset($_POST['ubezpieczenie']) ? 'Tak' : 'Nie';
        $tresc_dodatkowe = "[$znacznik_czasu] Transport: {$_POST['transport']} (Z: {$_POST['miejsce_wyjazdu']} Do: {$_POST['miejsce_powrotu']}) | Dieta: {$_POST['dieta']} | Specjalne: {$_POST['potrzeby_specjalne']} | Jezyk: {$_POST['jezyk']} | Ubezpieczenie: $status_ubezpieczenia | Uwagi: {$_POST['uwagi']}\n";
        file_put_contents('dane_dodatkowe.txt', $tresc_dodatkowe, FILE_APPEND);
        
        $sukces = "Formularz wysłany i zapisany poprawnie!";
        $_POST = []; 
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Formularz Podróży</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="kontener-glowny">
    <h2>FORMULARZ PODRÓŻY</h2>
    
    <?php if (!empty($sukces)): ?>
        <div class="komunikat-sukces"><?= $sukces ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        
        <fieldset>
            <legend>Dane osobowe</legend>
            
            <label>Imię <small class="gwiazdka">*</small></label>
            <input type="text" name="imie" class="<?= isset($bledy['imie']) ? 'input-blad' : '' ?>" value="<?= htmlspecialchars($_POST['imie'] ?? '') ?>">
            <span class="error-text"><?= $bledy['imie'] ?? '' ?></span>
            
            <label>Nazwisko <small class="gwiazdka">*</small></label>
            <input type="text" name="nazwisko" class="<?= isset($bledy['nazwisko']) ? 'input-blad' : '' ?>" value="<?= htmlspecialchars($_POST['nazwisko'] ?? '') ?>">
            <span class="error-text"><?= $bledy['nazwisko'] ?? '' ?></span>
            
            <label>Data urodzenia <small class="gwiazdka">*</small></label>
            <input type="date" name="data_urodzenia" class="<?= isset($bledy['data_urodzenia']) ? 'input-blad' : '' ?>" value="<?= htmlspecialchars($_POST['data_urodzenia'] ?? '') ?>">
            <span class="error-text"><?= $bledy['data_urodzenia'] ?? '' ?></span>
            
            <label>Obywatelstwo</label>
            <input type="text" name="obywatelstwo" value="<?= htmlspecialchars($_POST['obywatelstwo'] ?? '') ?>">
            
            <label>PESEL <small class="gwiazdka">*</small></label>
            <input type="text" name="pesel" class="<?= isset($bledy['pesel']) ? 'input-blad' : '' ?>" value="<?= htmlspecialchars($_POST['pesel'] ?? '') ?>">
            <span class="error-text"><?= $bledy['pesel'] ?? '' ?></span>
            
            <label>Telefon kontaktowy</label>
            <input type="text" name="telefon" value="<?= htmlspecialchars($_POST['telefon'] ?? '') ?>">
            
            <label>Adres e-mail <small class="gwiazdka">*</small></label>
            <input type="email" name="email" class="<?= isset($bledy['email']) ? 'input-blad' : '' ?>" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <span class="error-text"><?= $bledy['email'] ?? '' ?></span>
        </fieldset>

        <fieldset>
            <legend>Informacje o podróży</legend>
            
            <label>Kraj / region docelowy <small class="gwiazdka">*</small></label>
            <input type="text" name="kraj" class="<?= isset($bledy['kraj']) ? 'input-blad' : '' ?>" value="<?= htmlspecialchars($_POST['kraj'] ?? '') ?>">
            <span class="error-text"><?= $bledy['kraj'] ?? '' ?></span>
            
            <label>Miejscowość <small class="gwiazdka">*</small></label>
            <input type="text" name="miejscowosc" class="<?= isset($bledy['miejscowosc']) ? 'input-blad' : '' ?>" value="<?= htmlspecialchars($_POST['miejscowosc'] ?? '') ?>">
            <span class="error-text"><?= $bledy['miejscowosc'] ?? '' ?></span>
            
            <label>Data rozpoczęcia podróży <small class="gwiazdka">*</small></label>
            <input type="date" name="data_start" class="<?= isset($bledy['data_start']) ? 'input-blad' : '' ?>" value="<?= htmlspecialchars($_POST['data_start'] ?? '') ?>">
            <span class="error-text"><?= $bledy['data_start'] ?? '' ?></span>
            
            <label>Data zakończenia podróży <small class="gwiazdka">*</small></label>
            <input type="date" name="data_koniec" class="<?= isset($bledy['data_koniec']) ? 'input-blad' : '' ?>" value="<?= htmlspecialchars($_POST['data_koniec'] ?? '') ?>">
            <span class="error-text"><?= $bledy['data_koniec'] ?? '' ?></span>
            
            <label>Liczba dni pobytu <small class="gwiazdka">*</small></label>
            <select name="dni" class="<?= isset($bledy['dni']) ? 'input-blad' : '' ?>">
                <option value="">-- wybierz --</option>
                <option value="7" <?= (($_POST['dni'] ?? '') == '7') ? 'selected' : '' ?>>7 dni</option>
                <option value="10" <?= (($_POST['dni'] ?? '') == '10') ? 'selected' : '' ?>>10 dni</option>
                <option value="14" <?= (($_POST['dni'] ?? '') == '14') ? 'selected' : '' ?>>14 dni</option>
            </select>
            <span class="error-text"><?= $bledy['dni'] ?? '' ?></span>
            
            <label>Cel podróży</label>
            <select name="cel_podrozy">
                <option value="wypoczynek" <?= (($_POST['cel_podrozy'] ?? '') == 'wypoczynek') ? 'selected' : '' ?>>Wypoczynek</option>
                <option value="zwiedzanie" <?= (($_POST['cel_podrozy'] ?? '') == 'zwiedzanie') ? 'selected' : '' ?>>Zwiedzanie</option>
                <option value="biznes" <?= (($_POST['cel_podrozy'] ?? '') == 'biznes') ? 'selected' : '' ?>>Biznes</option>
                <option value="inny" <?= (($_POST['cel_podrozy'] ?? '') == 'inny') ? 'selected' : '' ?>>Inny</option>
            </select>
        </fieldset>

        <fieldset>
            <legend>Zakwaterowanie</legend>
            
            <label>Rodzaj zakwaterowania</label>
            <input type="text" name="rodzaj_zakwaterowania" placeholder="hotel, apartament, pensjonat" value="<?= htmlspecialchars($_POST['rodzaj_zakwaterowania'] ?? '') ?>">
            
            <label>Standard obiektu (gwiazdki/kategoria)</label>
            <input type="text" name="standard_obiektu" placeholder="np. 4 gwiazdki" value="<?= htmlspecialchars($_POST['standard_obiektu'] ?? '') ?>">
            
            <label>Liczba osób w pokoju <small class="gwiazdka">*</small></label>
            <input type="number" name="liczba_osob" class="<?= isset($bledy['liczba_osob']) ? 'input-blad' : '' ?>" value="<?= htmlspecialchars($_POST['liczba_osob'] ?? '') ?>" placeholder="np. 2">
            <span class="error-text"><?= $bledy['liczba_osob'] ?? '' ?></span>
            
            <label>Preferencje pokoju</label>
            <select name="pref_pokoju">
                <option value="jednoosobowy" <?= (($_POST['pref_pokoju'] ?? '') == 'jednoosobowy') ? 'selected' : '' ?>>Jednoosobowy</option>
                <option value="dwuosobowy" <?= (($_POST['pref_pokoju'] ?? '') == 'dwuosobowy') ? 'selected' : '' ?>>Dwuosobowy</option>
                <option value="rodzinny" <?= (($_POST['pref_pokoju'] ?? '') == 'rodzinny') ? 'selected' : '' ?>>Rodzinny</option>
            </select>
        </fieldset>

        <fieldset>
            <legend>Transport</legend>
            
            <label>Środek transportu</label>
            <select name="transport">
                <option value="samolot" <?= (($_POST['transport'] ?? '') == 'samolot') ? 'selected' : '' ?>>Samolot</option>
                <option value="autokar" <?= (($_POST['transport'] ?? '') == 'autokar') ? 'selected' : '' ?>>Autokar</option>
                <option value="wlasny" <?= (($_POST['transport'] ?? '') == 'wlasny') ? 'selected' : '' ?>>Własny</option>
            </select>
            
            <label>Miejsce wyjazdu</label>
            <input type="text" name="miejsce_wyjazdu" value="<?= htmlspecialchars($_POST['miejsce_wyjazdu'] ?? '') ?>">
            
            <label>Miejsce powrotu</label>
            <input type="text" name="miejsce_powrotu" value="<?= htmlspecialchars($_POST['miejsce_powrotu'] ?? '') ?>">
        </fieldset>

        <fieldset>
            <legend>Preferencje i potrzeby</legend>
            
            <label>Dieta (brak, wegetariańska itp.)</label>
            <input type="text" name="dieta" value="<?= htmlspecialchars($_POST['dieta'] ?? '') ?>">
            
            <label>Potrzeby specjalne</label>
            <textarea name="potrzeby_specjalne" rows="2"><?= htmlspecialchars($_POST['potrzeby_specjalne'] ?? '') ?></textarea>
            
            <label>Język komunikacji</label>
            <input type="text" name="jezyk" value="<?= htmlspecialchars($_POST['jezyk'] ?? '') ?>">
        </fieldset>

        <fieldset>
            <legend>Ubezpieczenie i zgody</legend>
            
            <label class="opcja-row">
                <input type="checkbox" name="ubezpieczenie" value="tak" <?= isset($_POST['ubezpieczenie']) ? 'checked' : '' ?>>
                <span>Ubezpieczenie turystyczne (Tak/Nie)</span>
            </label>
            
            <label class="opcja-row">
                <input type="checkbox" name="zgoda_rodo" value="zaakceptowano" <?= isset($_POST['zgoda_rodo']) ? 'checked' : '' ?>>
                <span>Zgoda na przetwarzanie danych (RODO) <small class="gwiazdka">*</small></span>
            </label>
            <span class="error-text"><?= $bledy['zgoda_rodo'] ?? '' ?></span>
        </fieldset>

        <div class="pelna-szerokosc">
            <label>Uwagi / komentarze:</label>
            <textarea name="uwagi" rows="3"><?= htmlspecialchars($_POST['uwagi'] ?? '') ?></textarea>
            
            <button type="submit">Zatwierdź i Wyślij Zgłoszenie</button>
        </div>
    </form>
</div>

<footer>
    Stronę wykonał: Aleksander Pabian 4c
</footer>

</body>
</html>