<?php

namespace Noweh\MarvelMemories;

use SQLite3;
use Exception;
use JsonException;
use RuntimeException;

class DBAdapter extends SQLite3
{
    /**
     * Constructor
     * Create table if not exists
     * @param string $filename
     * @param int $flags
     * @param string $encryptionKey
     */
    public function __construct(
        string $filename,
        int $flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE,
        string $encryptionKey = ''
    ) {
        parent::__construct($filename, $flags, $encryptionKey);

        // Create table if not exists
        $this->query(
            "CREATE TABLE IF NOT EXISTS covers (
                cover_id INTEGER PRIMARY KEY,
                sql_time TIMESTAMP default CURRENT_TIMESTAMP not null
            );"
        );
    }

    /**
     * Insert the coverId in database
     * @param string $coverId
     * @return bool
     * @throws JsonException
     */
    public function addCoverId(string $coverId): bool
    {
        try {
            $stmt = $this->prepare("INSERT INTO covers (cover_id) VALUES (:coverId)");
            if ($stmt) {
                $stmt->bindValue(':coverId', $coverId, SQLITE3_INTEGER);
                if ($stmt->execute() !== false) {
                    return true;
                }
            }
        } catch (Exception $e) {
            throw new RuntimeException(__METHOD__ . ': '. json_encode($e->getMessage(), JSON_THROW_ON_ERROR));
        }

        throw new RuntimeException(__METHOD__ . ': unable to execute statement');
    }

    /**
     * Search if the coverId exists
     * @param string $coverId
     * @return array<int, string>|false
     * @throws JsonException
     */
    public function searchCoverId(string $coverId): array|false
    {
        try {
            $stmt = $this->prepare("SELECT * FROM covers WHERE cover_id=:coverId");
            if ($stmt) {
                $stmt->bindValue(':coverId', $coverId, SQLITE3_INTEGER);
                $result = $stmt->execute();
                if ($result) {
                    return $result->fetchArray();
                }
            }
        } catch (Exception $e) {
            throw new RuntimeException(__METHOD__ . ': '. json_encode($e->getMessage(), JSON_THROW_ON_ERROR));
        }

        throw new RuntimeException(__METHOD__ . ': unable to execute statement');
    }
}
