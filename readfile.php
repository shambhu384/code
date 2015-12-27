<?php
//get 3 commands from user
for ($i=0; $i < 3; $i++) {
            $line = readline("Command: ");
                    readline_add_history($line);
}

$array = [];

//dump history
print_r(readline_list_history());

//dump variables
