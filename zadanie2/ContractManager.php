<?php

class ContractManager
{
    private PDO $db;
    private string $bgColor;

    public function __construct(PDO $db, string $bgColor = '#ffffff')
    {
        $this->db = $db;
        $this->bgColor = $bgColor;
    }

    public function showContracts(int $action, ?int $sort = null, ?int $id = null): void
    {
        if ($action === 5) {
            $this->showFilteredContracts($sort, $id);
        } else {
            $this->showAllContracts();
        }
    }

    private function showFilteredContracts(?int $sort, ?int $id): void
    {
        $query = "SELECT * FROM contracts WHERE kwota > 10";
        $params = [];

        if ($id !== null) {
            $query .= " AND id = :id";
            $params[':id'] = $id;
        }

        $query .= $this->getOrderByClause($sort);

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        $this->renderTable($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function showAllContracts(): void
    {
        $query = "SELECT * FROM contracts ORDER BY id";
        $stmt = $this->db->query($query);

        $this->renderTable($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function getOrderByClause(?int $sort): string
    {
        return match ($sort) {
            1 => " ORDER BY nazwa_przedsiebiorcy, NIP DESC",
            2 => " ORDER BY kwota DESC",
            default => ""
        };
    }

    private function renderTable(array $data): void
    {
        echo "<html><body bgcolor='{$this->bgColor}'>";
        echo "<br>";
        echo "<table width='95%'>";

        foreach ($data as $row) {
            echo '<tr>';
            echo "<td>{$row['id']}</td>";
            echo '<td>' . htmlspecialchars($row['nazwa_przedsiebiorcy']);

            if (isset($row['kwota']) && $row['kwota'] > 5) {
                echo " {$row['kwota']}";
            }

            echo '</td></tr>';
        }

        echo '</table></body></html>';
    }
}

// Przykład użycia:
try {
    // Ustawienia połączenia z bazą danych
    $dsn = 'mysql:host=localhost;dbname=test_db;charset=utf8mb4';
    $username = 'root';
    $password = '';

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $bgColor = '#f0f0f0';
    $contractManager = new ContractManager($pdo, $bgColor);

    // Dane wejściowe
    $action = $_GET['akcja'] ?? null;
    $sort = $_GET['sort'] ?? null;
    $id = $_GET['i'] ?? null;

    if (is_numeric($action)) {
        $contractManager->showContracts((int)$action, $sort ? (int)$sort : null, $id ? (int)$id : null);
    } else {
        throw new InvalidArgumentException("Nieprawidłowa akcja");
    }

} catch (PDOException $e) {
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
} catch (Exception $e) {
    echo "Wystąpił błąd: " . $e->getMessage();
}
