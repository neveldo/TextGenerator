<?php
/**
 * Simple API using TextGenerator
 * HTTP post parameters :
 *   string template : The template to use to generate the texts
 *   array data :
 * [
 *   {'tag1': 'value', 'tag2': 'value'},
 *   {'tag1': 'value', 'tag2': 'value'},
 *   ...
 * ]
 *
 * Return following JSON object :
 * {
 *   'result': [
 *     'text 1',
 *     'text 1',
 *     ...,
 *   ],
 *   'error': ''
 * }
 */

mb_internal_encoding("UTF-8");

if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    echo "Please run 'composer install' on the root directory before running the sample script.";
    return;
}

require __DIR__ . '/../../vendor/autoload.php';

use Neveldo\TextGenerator\TextGenerator;

$output = [
    'result' => [],
    'error' => '',
];

if (!isset($_POST['template']) || !isset($_POST['data'])) {
    $output['error'] = 'Missing POST attribute "template" or "data".';
}

if ($output['error'] === '') {
    $textGenerator = new TextGenerator();
    $textGenerator->compile($_POST['template']);

    try {
        /** @var array<array<string>> */
        $data = json_decode((string) $_POST['data'], true);

        foreach($data as $row) {
            $output['result'][] = $textGenerator->generate($row);
        }

    } catch (Exception $e) {
        $output['error'] = $e->getMessage();
    }
}

echo json_encode($output);
