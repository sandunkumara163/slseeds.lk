<?php
$env = file_get_contents('.env');
$env .= "\nPAYHERE_MERCHANT_ID=1238251\nPAYHERE_SECRET=MTcxNTcyMjQ5MDg3NzQxNTIyNzczMzIzMjUzMTE2MjAwMjU5\n";
file_put_contents('.env', $env);
