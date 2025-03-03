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
} elseif (isset($_POST['usun'])) {
    $id = $_POST['id'];
    $zap2 = mysqli_query($pol, "DELETE FROM zadania WHERE id=$id;");
} elseif (isset($_POST['zmien'])) {
    $id = $_POST['id'];
    $czyZrobione = $_POST['czyZrobione'] ? 0 : 1;
    $zap3 = mysqli_query($pol, "UPDATE zadania SET czyZrobione=$czyZrobione WHERE id=$id;");
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
                        <button type="submit" name="usun" class="delete-btn">Usuń</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
<?php mysqli_close($pol); ?>