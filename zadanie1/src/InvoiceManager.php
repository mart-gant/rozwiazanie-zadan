<?php

class InvoiceManager
{
    private PDO $pdo;

    public function __construct(Database $database)
    {
        $this->pdo = $database->getPdo();
    }

    public function setupDatabase(): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS payments, invoice_items, invoices, customers;");
        $this->pdo->exec("CREATE TABLE customers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            bank_account VARCHAR(255) NOT NULL,
            nip VARCHAR(20) NOT NULL
        );");
        // Pozostałe tabelki tutaj...
    }

    public function insertSampleData(): void
    {
        $this->pdo->exec("INSERT INTO customers (name, bank_account, nip) VALUES
            ('ABC Sp. z o.o.', '12345678901234567890123456', '1234567890'),
            ('XYZ S.A.', '23456789012345678901234567', '9876543210');");
        // Wstawianie danych przykładowych...
    }

    public function getOverpayments(): array
    {
        $query = "SELECT c.name, SUM(p.amount) - IFNULL(SUM(i.total_amount), 0) AS overpayment
                  FROM customers c
                  LEFT JOIN payments p ON c.id = p.customer_id
                  LEFT JOIN invoices i ON c.id = i.customer_id
                  GROUP BY c.id
                  HAVING overpayment > 0;";
        return $this->pdo->query($query)->fetchAll();
    }

    // Metody getUnderpayments() i getOverdueInvoices()...
}
