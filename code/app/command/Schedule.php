<?php
declare(strict_types=1);

namespace app\command;

use app\model\TaskModel;
use app\model\ToolsCheckModel;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;
use Throwable;

/**
 * 常驻扫描调度器：自动生成扫描任务并逐个执行
 * 用法: php think schedule [--interval=3] [--limit=5]
 * 与 script.sh 的 while 循环等价，但常驻单进程、无需反复启动框架
 */
class Schedule extends Command
{
    private bool $running = true;

    protected function configure()
    {
        $this->setName('schedule')
            ->addOption('interval', null, 4, '轮询间隔（秒）', 3)
            ->addOption('limit', null, 4, '每轮最多派发任务数', 5)
            ->setDescription('常驻扫描调度器：自动生成并执行扫描任务');
    }

    protected function execute(Input $input, Output $output): void
    {
        $interval = max(1, (int)$input->getOption('interval'));
        $limit = max(1, (int)$input->getOption('limit'));

        // 优雅退出
        if (function_exists('pcntl_signal')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGTERM, function () { $this->running = false; });
            pcntl_signal(SIGINT, function () { $this->running = false; });
        }

        $output->writeln('<info>[' . date('Y-m-d H:i:s') . '] 扫描调度器已启动，每 ' . $interval . ' 秒轮询一次</info>');
        $tasks = Scan::getScanTasks();

        while ($this->running) {
            $start = microtime(true);
            try {
                // 1. 生成扫描任务
                TaskModel::autoAddTask();

                // 2. 消费待执行任务
                $list = Db::table('task_scan')
                    ->where('status', 0)
                    ->limit($limit)
                    ->order('id', 'asc')
                    ->select()->toArray();
                foreach ($list as $task) {
                    if (!$this->running) break;
                    $this->runTask($tasks, $task, $output);
                }
            } catch (Throwable $e) {
                $output->writeln('<error>[' . date('Y-m-d H:i:s') . '] 调度异常: ' . $e->getMessage() . '</error>');
            }

            // 3. 轮询间隔（任务执行耗时可能很长，这里只在无任务时等待）
            $elapsed = (microtime(true) - $start);
            if ($elapsed < $interval) {
                usleep((int)(($interval - $elapsed) * 1000000));
            }
        }
        $output->writeln('<info>[' . date('Y-m-d H:i:s') . '] 调度器已停止</info>');
    }

    private function runTask(array $tasks, array $task, Output $output): void
    {
        $func = $task['tool'];
        $detail = isset($tasks[$func]) ? $tasks[$func] : null;
        if ($detail === null) {
            // 未知工具：标记完成避免死循环
            Db::table('task_scan')->where('id', $task['id'])->update(['status' => 2]);
            $output->writeln('[' . date('Y-m-d H:i:s') . '] 未知任务工具已跳过: ' . $func);
            return;
        }
        if ($detail[0] === TaskModel::class) {
            // create_task / start_task 由调度器内部逻辑替代，跳过派发
            $output->writeln('[' . date('Y-m-d H:i:s') . '] 调度任务无需执行: ' . $func);
            Db::table('task_scan')->where('id', $task['id'])->update(['status' => 2]);
            return;
        }

        // 工具不可用（未安装且无内置引擎）则跳过
        if (isset($detail[2]) && !ToolsCheckModel::checkToolInstalled($detail[2])) {
            $output->writeln('<error>[' . date('Y-m-d H:i:s') . '] 工具不可用已跳过: ' . $detail[2] . ' (任务 ' . $func . ')</error>');
            Db::table('task_scan')->where('id', $task['id'])->update(['status' => 2]);
            return;
        }

        Db::table('task_scan')->where('id', $task['id'])->update(['status' => 1]);
        $output->writeln('[' . date('Y-m-d H:i:s') . '] 开始执行任务: ' . $func . ' (id=' . $task['id'] . ')');
        try {
            call_user_func([$detail[0], $detail[1]]);
            $output->writeln('[' . date('Y-m-d H:i:s') . '] 任务执行完成: ' . $func . ' (id=' . $task['id'] . ')');
        } catch (Throwable $e) {
            $output->writeln('<error>[' . date('Y-m-d H:i:s') . '] 任务执行错误: ' . $func . ' - ' . $e->getMessage() . ' @' . $e->getFile() . ':' . $e->getLine() . '</error>');
            Db::table('task_scan')->where('id', $task['id'])->update(['status' => 2]);
        }
    }
}
