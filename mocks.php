<?php

/**
 * A mock for send_email function
 *
 * @param string $email
 * @param string $from
 * @param string $to
 * @param string $subj
 * @param string $body
 */
function send_email( string $email, string $from, string $to, string $subj, string $body ) {
    echo "Email $email is sent to $to from $from";
}

/**
 * A mock for check_email function
 *
 * @param string $email - email
 */
function check_email(string $email) {
    echo "Email $email is valid";
}
