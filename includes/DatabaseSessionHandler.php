<?php
// includes/DatabaseSessionHandler.php
// Custom session handler that stores sessions in the database.
// This is required for serverless environments like Vercel where
// filesystem-based sessions do not persist between requests.

class DatabaseSessionHandler implements SessionHandlerInterface
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string|false
    {
        $stmt = $this->pdo->prepare('SELECT payload FROM sessions WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? base64_decode($row['payload']) : '';
    }

    public function write($id, $data): bool
    {
        // Use REPLACE INTO for MySQL/TiDB (INSERT or UPDATE)
        $stmt = $this->pdo->prepare(
            'REPLACE INTO sessions (id, payload, last_activity) VALUES (?, ?, ?)'
        );
        return $stmt->execute([$id, base64_encode($data), time()]);
    }

    public function destroy($id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function gc($maxLifetime): int|false
    {
        $expiry = time() - $maxLifetime;
        $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE last_activity < ?');
        $stmt->execute([$expiry]);
        return $stmt->rowCount();
    }
}
