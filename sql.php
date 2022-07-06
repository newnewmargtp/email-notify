<?php
const DB_COUNT_QUERY = 'SELECT COUNT(*) as num 
FROM
  users u INNER JOIN emails e ON u.email = e.email
WHERE
  u.confirmed = 1
  AND e.valid = 1
  AND e.checked = 1
  AND (NOW() + INTERVAL 2 DAY < u.validts < NOW() + INTERVAL 3 DAY)';

const DB_FETCH_QUERY = '
SELECT u.username as username, u.email as email
FROM
  users u INNER JOIN emails e ON u.email = e.email
WHERE
  u.confirmed = 1
  AND e.valid = 1
  AND e.checked = 1
  AND (NOW() + INTERVAL 2 DAY < u.validts < NOW() + INTERVAL 3 DAY)
LIMIT '. DB_BATCH_NUMBER . ' OFFSET $OFFSET ';

const DB_EMAILS_COUNT_QUERY = 'SELECT COUNT(*) as num 
FROM
  users u INNER JOIN emails e ON u.email = e.email
WHERE
  u.confirmed = 1
  AND e.checked = 0
  AND (NOW() + INTERVAL 2 DAY < u.validts < NOW() + INTERVAL 3 DAY)';

const DB_EMAILS_FETCH_QUERY = '
SELECT u.email as email
FROM
  users u INNER JOIN emails e ON u.email = e.email
WHERE
  u.confirmed = 1
  AND e.checked = 0
  AND (NOW() + INTERVAL 2 DAY < u.validts < NOW() + INTERVAL 3 DAY)
LIMIT '. DB_BATCH_NUMBER . ' OFFSET $OFFSET ';


const DB_VARS_OFFSET = '$OFFSET';

const DB_EMAILS_UPDATE_QUERY = '
UPDATE emails set valid = $valid, checked = 1 where email = $email';
const DB_VARS_EMAIL = '$email';
const DB_VARS_VALID = '$valid';
