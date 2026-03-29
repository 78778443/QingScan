<?php
declare(strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class LoadTools extends Command
{
    protected function configure()
    {
        $this->setName('tools:load')->setDescription('Load tools from ToolsCode/*/config.yaml');
    }

    protected function execute(Input $input, Output $output)
    {
        $dir = root_path() . 'ToolsCode';
        $files = glob($dir . '/*/config.yaml');

        if (empty($files)) {
            $output->writeln('No config files found in ' . $dir);
            return;
        }

        $count = 0;
        foreach ($files as $file) {
            $config = $this->parseYaml($file);
            if (empty($config['name'])) continue;

            $image = $config['image'] ?? '';
            $command = str_replace('{image}', $image, $config['command'] ?? '');

            Db::execute(
                "INSERT INTO scan_tool (tool_name, tool_label, description, start_command, is_enabled)
                 VALUES (?, ?, ?, ?, 1)
                 ON DUPLICATE KEY UPDATE
                     tool_label = VALUES(tool_label),
                     description = VALUES(description),
                     start_command = VALUES(start_command)",
                [$config['name'], $config['label'] ?? $config['name'], $config['description'] ?? '', $command]
            );

            $output->writeln("+ " . $config['name']);
            $count++;
        }

        $output->writeln("Loaded {$count} tools");
    }

    private function parseYaml(string $file): array
    {
        $result = [];
        $content = file_get_contents($file);
        foreach (explode("\n", $content) as $line) {
            if (preg_match('/^(\w+):\s*(.*)$/', $line, $m)) {
                $result[$m[1]] = trim($m[2]);
            }
        }
        return $result;
    }
}
