<?php
namespace app\commands;

use yii\console\Controller;

class ResourceController extends Controller
{
    private $inDir;
    private $outDir;

    public function init()
    {
        parent::init();
        $base = dirname(__DIR__);
        $this->inDir = $base . '/resources';
        $this->outDir = $base . '/web';
    }

    public function actionIndex()
    {
        $this->actionJs();
        $this->actionCss();
    }

    public function actionJs()
    {
        $this->exec(function($inPath, $outPath) {
            if (!preg_match('/\.js$/', $inPath)) {
                return;
            }
            if (!file_exists(dirname($outPath))) {
                mkdir(dirname($outPath), 0755, true);
            }
            $cmdline = sprintf(
                '/usr/bin/env %s %s -o %s -b beautify=false,ascii-only=true -m -c',
                escapeshellarg('uglifyjs'),
                escapeshellarg($inPath),
                escapeshellarg($outPath)
            );
            $lines = [];
            $status = -1;
            exec($cmdline, $lines, $status);
            if ($status !== 0) {
                die(1);
            }
        });
    }

    public function actionCss()
    {
        $this->exec(function($inPath, $outPath) {
            if (!preg_match('/\.css$/', $inPath)) {
                return;
            }
            if (!file_exists(dirname($outPath))) {
                mkdir(dirname($outPath), 0755, true);
            }
            $cmdline = sprintf(
                '/usr/bin/env %s %s -o %s -s',
                escapeshellarg('cleancss'),
                escapeshellarg($inPath),
                escapeshellarg($outPath)
            );
            $lines = [];
            $status = -1;
            exec($cmdline, $lines, $status);
            if ($status !== 0) {
                die(1);
            }
        });
    }

    private function exec($callback)
    {
        $runRecursive = function ($inDir, $outDir, $callback) use (&$runRecursive) {
            foreach (new \DirectoryIterator($inDir) as $entry) {
                if ($entry->isDot()) {
                    continue;
                }

                $inPath = $inDir . '/' . $entry->getBaseName();
                $outPath = $outDir . '/' . $entry->getBaseName();
                if ($entry->isDir()) {
                    call_user_func($runRecursive, $inPath, $outPath, $callback);
                } else {
                    if (file_exists($outPath) && filemtime($inPath) <= filemtime($outPath)) {
                        continue;
                    }
                    call_user_func($callback, $inPath, $outPath);
                }
            }
        };

        call_user_func($runRecursive, $this->inDir, $this->outDir, $callback);
    }
}
