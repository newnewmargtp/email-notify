<?php

/**
 * Get the full amount of lines
 *
 * @param mysqli $conn - connection
 * @param string $query - count query
 *
 * @return int
 */
function getCount(mysqli $conn, string $query): int {
    $result = mysqli_query($conn, $query);
    if (!$result) {
        logError('Query error: '.mysqli_error($conn)) && correctExit(1);
    }
    $row = mysqli_fetch_array($result);

    return (int) $row['num'];
}

/**
 * Check all processes in given &$pids array and remove pid if the Linux process is over
 *
 * @param array $pids - the reference to array of pids
 */
function cleanPids(array &$pids) {
    foreach ($pids as $k => $p) {
        if (!file_exists("/proc/$p")) {
            unset($pids[$k]);
        }
    }
}

/**
 * Opens MySQL connection, returns mysqli connection
 *
 * @return mysqli
 */
function getConnection(): mysqli {
    static $conn;
    if($conn) {
        return $conn;
    }

    $conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DB);
    if (!$conn) {
        logError('Connection error: '.mysqli_connect_error()) && correctExit(1);
    }

    return $conn;
}

/**
 * Logging errors
 *
 * @param string $text - error text
 */
function logError(string $text) {
    $text = "[emails-service] Error during emails sending: $text\n";
    file_put_contents(LOG_FILE, $text.PHP_EOL , FILE_APPEND);
    error_log($text);
}

/**
 * Removing curent pid file and exiting
 *
 * @param int $exitCode - exit code
 */
function correctExit(int $exitCode){
    unlink(VALIDATE_PID_FILE);

    exit($exitCode);
}
