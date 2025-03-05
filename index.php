<?php
$server = "localhost";
$uzytkownik = "root";
$haslo = "";
$nazwadb = "todo";

$pol = mysqli_connect($server, $uzytkownik, $haslo, $nazwadb);
if (!$pol) {
    die("Błąd połączenia: " . mysqli_connect_error());
}

if (isset($_POST['dodaj'])) {
    $zadanie = $_POST['zadanie'];
    $zap1 = mysqli_query($pol, "INSERT INTO zadania (zadanie, czyZrobione) VALUES ('$zadanie', 0);");
}

elseif (isset($_POST['usun'])) {
    $id = $_POST['id'];
    $zap2 = mysqli_query($pol, "DELETE FROM zadania WHERE id=$id;");
}

elseif (isset($_POST['zmien'])) {
    $id = $_POST['id'];
    $czyZrobione = $_POST['czyZrobione'] ? 0 : 1;

    if ($czyZrobione == 0) {
        $data_zakonczenia = 'NULL';
    } else {
        $data_zakonczenia = "NOW()";
    }

    $query = "UPDATE zadania SET czyZrobione=$czyZrobione, data_zakonczenia=$data_zakonczenia WHERE id=$id";
    $zap3 = mysqli_query($pol, $query);

    if (!$zap3) {
        die("Błąd zapytania SQL: " . mysqli_error($pol));
    }
}

elseif (isset($_POST['edycja'])) {
    $id = $_POST['id'];
    $zadanie = $_POST['zadanie'];
    $zap4 = mysqli_query($pol, "UPDATE zadania SET zadanie='$zadanie' WHERE id=$id;");
}

if (isset($_POST['dodaj_komentarz'])) {
    $id_zadania = $_POST['id_zadania'];
    $komentarz = $_POST['komentarz'];
    $zap5 = mysqli_query($pol, "INSERT INTO komentarze (zadanie_id, komentarz) VALUES ($id_zadania, '$komentarz');");
}

$wynik = mysqli_query($pol, "SELECT * FROM zadania;");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista TODO</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Lista TODO</h2>
        <form method="POST" class="task-form">
            <input type="text" name="zadanie" placeholder="Nowe zadanie" required>
            <button type="submit" name="dodaj">Dodaj</button>
        </form>
        <ul class="task-list">
            <?php while ($wiersz = mysqli_fetch_assoc($wynik)): ?>
                <li class="<?= $wiersz['czyZrobione'] ? 'done' : '' ?>">
                    <?= htmlspecialchars($wiersz['zadanie']) ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $wiersz['id'] ?>">
                        <input type="hidden" name="czyZrobione" value="<?= $wiersz['czyZrobione'] ?>">
                        <button type="submit" name="zmien" class="toggle-btn">
                            <?= $wiersz['czyZrobione'] ? 'Cofnij' : 'Zrobione' ?>
                        </button>
                        <button type="button" class="toggle-btn" onclick="edytujZadanie(<?= $wiersz['id'] ?>, '<?= htmlspecialchars($wiersz['zadanie']) ?>')">
                            Edytuj
                        </button>
                        <button type="submit" name="usun" class="delete-btn">Usuń</button>
                    </form>

                    <form method="POST" class="comment-form">
                        <input type="hidden" name="id_zadania" value="<?= $wiersz['id'] ?>">
                        <textarea name="komentarz" placeholder="Dodaj komentarz" required></textarea>
                        <button type="submit" name="dodaj_komentarz">Dodaj komentarz</button>
                    </form>
                    <ul class="comment-list">
                        <?php
                            $komentarze = mysqli_query($pol, "SELECT * FROM komentarze WHERE zadanie_id = " . $wiersz['id']);
                            while ($komentarz = mysqli_fetch_assoc($komentarze)): ?>
                                <li><?= htmlspecialchars($komentarz['komentarz']) ?> <em><?= $komentarz['data_utworzenia'] ?></em></li>
                        <?php endwhile; ?>
                    </ul>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <div id="editObszar" style="display:none;">
        <div class="modal-content">
            <h3>Edytuj zadanie</h3>
            <form method="POST">
                <input type="hidden" name="id" id="editId">
                <input type="text" name="zadanie" id="editNazwa" required>
                <button type="submit" name="edycja">Zapisz zmiany</button>
                <button type="button" onclick="zamknijEditObszar()">Anuluj</button>
            </form>
        </div>
    </div>

    <script>
        function edytujZadanie(id, zadanie) {
            document.getElementById('editId').value = id;
            document.getElementById('editNazwa').value = zadanie;
            document.getElementById('editObszar').style.display = 'block';
        }

        function zamknijEditObszar() {
            document.getElementById('editObszar').style.display = 'none';
        }
    </script>
</body>
</html>

<?php mysqli_close($pol); ?>
