<?php
date_default_timezone_set('Asia/Tokyo');

$data = [];
foreach (new DirectoryIterator(__DIR__ . '/data') as $entry) {
    if (!$entry->isFile() || !preg_match('/^results\.(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})\.json$/', $entry->getBaseName(), $match)) {
        continue;
    }

    $json = json_decode(file_get_contents($entry->getPathName()), true);
    $red = 0;
    $green = 0;
    foreach ($json as $win) {
        switch ($win['win_team_name']) {
            case '赤いきつね':
                ++$red;
                break;

            case '緑のたぬき':
                ++$green;
                break;

            default:
                echo $win['win_team_name'] . " (゜Д゜) ハア??\n";
                exit(1);
        }
    }

    $data[] = (object)[
        'sha256sum'     => base64_encode(hash_file('sha256', $entry->getPathName(), true)),
        'downloaded_at' => mktime($match[4], $match[5], $match[6], $match[2], $match[3], $match[1]),
        'wins' => (object)[
            'r' => $red,
            'g' => $green,
        ]
    ];
}

usort($data, function ($a, $b) {
    return $a->downloaded_at - $b->downloaded_at;
});

echo 'INSERT INTO "official_data" VALUES ' . "\n";
foreach ($data as $i => $datum) {
    if ($i > 0) {
        echo ",\n";
    }
    printf(
        '    ( %d, %d, %s, %d )',
        $i + 1, // id
        2,      // fest_id
        e($datum->sha256sum), // hash
        $datum->downloaded_at
    );
}
echo ";\n\n";

echo 'INSERT INTO "official_win_data" VALUES ' . "\n";
foreach ($data as $i => $datum) {
    if ($i > 0) {
        echo ",\n";
    }
    printf(
        "    ( %d, %d, %d ),\n",
        $i + 1, // data_id
        1, // color_id
        $datum->wins->r
    );
    printf(
        "    ( %d, %d, %d )",
        $i + 1, // data_id
        2, // color_id
        $datum->wins->g
    );
}
echo ";\n";

function e($text)
{
    // このプログラムは入力が事実上安全で書き捨てなのでこれで問題ないが
    // よいこはマネしちゃダメ。
    // ちゃんとしたエスケープ関数を使いましょう
    return "'" . str_replace("'", "''", $text) . "'";
}
