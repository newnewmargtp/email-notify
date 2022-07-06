<?php
include "config.php";
include "sql.php";
include "mocks.php";
include "common-functions.php";

const EMAIL_REGEXP = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

if(file_exists(VALIDATE_PID_FILE)) {
    logError('The same process is running or the last execution crashed, check logs, exiting');
}
file_put_contents(VALIDATE_PID_FILE, getmypid());

$conn = getConnection();
$count = getCount($conn, DB_EMAILS_COUNT_QUERY);
$batches = ceil($count / DB_BATCH_NUMBER); // Number of while-loop calls - around 120.
for ($i = 0; $i <= $batches; $i++) {
    $offset = $i * DB_BATCH_NUMBER;
    $query = str_replace(DB_VARS_OFFSET, $offset, DB_EMAILS_FETCH_QUERY);

    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
    processResult($result);
}

mysqli_close($conn);

/**
 * Update emails info in multithreading mode and wait until all the batch is completed
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
            fetchAndUpdate($row, $pids);
        }

        cleanPids($pids);
    } while ($pids);
}

/**
 * Fetching a row from query result and updating email status in fork process
 *
 * @param array $row - string array with result
 * @param array $pids - reference to array of pids
 */
function fetchAndUpdate(array $row, array &$pids) {
    $pids[] = pcntl_fork();
    if (!$pids[count($pids) - 1]) {
        update($row[0]);

        exit();
    }
}

/**
 * Validate email
 *
 * @param  string  $email - user's email
 *
 */
function update(string $email) {
    if (!$email) {
        logError('Empty email is given');

        return;
    }

    if (!preg_match(EMAIL_REGEXP, $email)) {
        updateDB($email, false);

        return;
    }

    $res = false;
    try {
        $res = check_email($email);
    } catch (Exception $e) {
        logError("exception during sending an email to $email: ". $e->getMessage());
    }

    updateDB($email, $res);
}

/**
 * Updating DB data
 *
 * @param string $email - email to udpate
 * @param bool $valid - valid or not
 */
function updateDB(string $email,bool $valid){
    $conn = getConnection();
    $query = str_replace(
        [DB_VARS_EMAIL, DB_VARS_VALID],
        [$email, $valid],
        DB_EMAILS_UPDATE_QUERY
    );

    $result = mysqli_query($conn, $query);
    if (!$result) {
        logError('Update emails query error: '.mysqli_error($conn)) && correctExit(1);
    }
}


