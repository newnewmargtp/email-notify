<?php
include "config.php";
include "sql.php";
include "mocks.php";
include "common-functions.php";

if(file_exists(VALIDATE_PID_FILE)) {
    logError('The same process is running or the last execution crashed, check logs, exiting');
}
file_put_contents(FETCH_PID_FILE, getmypid());

$conn = getConnection();
$count = getCount($conn, DB_COUNT_QUERY);
$batches = ceil($count / DB_BATCH_NUMBER); // Number of while-loop calls - around 120.
for ($i = 0; $i <= $batches; $i++) {
    $offset = $i * DB_BATCH_NUMBER;
    $query = str_replace(DB_VARS_OFFSET, $offset, DB_FETCH_QUERY);

    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
    processResult($result);
}

mysqli_close($conn);

/**
 * Send emails in multithreading mode and wait until all the batch is completed
 *
 * @param mysqli_result $result - mysql query ref
 *
 */
function processResult(mysqli_result $result) {
    $pids = [];
    do {
        if (count($pids) < THREADS_NUMBER
            && $row = mysqli_fetch_assoc($result)
        ) {
            fetchAndSend($row, $pids);
        }

        cleanPids($pids);
    } while ($pids);
}

/**
 * Fetching a row from query result and sending an email in fork process
 *
 * @param array $row - string array with result
 * @param array $pids - reference to array of pids
 */
function fetchAndSend(array $row, array &$pids) {
    $pids[] = pcntl_fork();
    if (!$pids[count($pids) - 1]) {
        send($row[0], $row[1]);

        exit();
    }
}

/**
 * Sending email
 *
 * @param string $user  - username to use in letter
 * @param  string  $email - user's email
 *
 */
function send(string $user, string $email) {

    if (!$user || !$email) {
        logError('Empty username is given, email is not sent');

        return;
    }

    $text = strtr(LETTER_TEMPLATE, [
        TEMPLATE_VARS_USER => $user
    ]);

    $body = str_replace(EMAIL_VARS_TEXT, $text, EMAIL_TEMPLATE);
    $to  = "$user<$email>";

    try {
        send_email($email, EMAIL_FROM, $to, $text, $body);
    } catch (Exception $e) {
        logError("exception during sending an email to $email: ". $e->getMessage());
    }
}


