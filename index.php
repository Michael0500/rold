<?php

include_once 'rold.php';

function runFile(string $path)
{
    run(file_get_contents($path));
}

function runPrompt()
{
    $h = fopen("php://stdin", 'r');
    for (;;) {
        print "> ";
      $line = fgets($h);
      if (empty($line) || $line === 'quit') break;
      run($line);
    }
}

function run(string $source)
{
    $scanner = new Scanner($source);
    $tokens = $scanner->scanTokens();

    // For now, just print the tokens.
    foreach ($tokens as $token) {
        /** @var Token $token */
        print $token;
        print PHP_EOL;
    }
    print PHP_EOL;

    $parser = new Parser($tokens);
    $program = $parser->parse();
    $program->execute();
}


if ($argc > 2) {
    print "Usage: rold [script]";
    exit(64);
} else if ($argc == 2) {
    runFile($argv[1]);
} else {
    runPrompt();
}
