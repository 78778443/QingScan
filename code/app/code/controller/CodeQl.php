<?php
declare (strict_types=1);

namespace app\code\controller;

use app\controller\Common;
use app\code\model\CodeQlModel;
use think\facade\Db;
use think\facade\View;
use think\Request;

class CodeQl extends Common
{
    public function index(Request $request)
    {
        $project_id = $request->param('project_id', 0);
        $where = empty($project_id) ? [] : ['project_id' => $project_id];
        $countList = CodeqlModel::getDetailCount($where);

        $where2 = [];

        $list = Db::name('codeql')->where($where)->where($where2)->paginate([
            'list_rows' => 10,
            'var_page' => 'page',
            'query' => $request->param(),
        ]);
        $bugList['list'] = $list->items();
        $page = $list->render();


        $data = ['countList' => $countList, 'bugList' => $bugList, 'page' => $page];

        return redirect('/web/');
    }


    public function detail(Request $request)
    {
        $id = $request->param('id');
        $where = ['id' => $id];
        $info = Db::table('codeql')->where($where)->find();

        if (empty($info)) {
            return redirect('/web/');
        }

        $info['codeFlows'] = json_decode($info['codeFlows'], true);
        if (!empty($info['codeFlows'])) {
            foreach ($info['codeFlows'] as &$item) {
                if (!empty($item['threadFlows'])) {
                    foreach ($item['threadFlows'] as &$val) {
                        if (!empty($val['locations'])) {
                            foreach ($val['locations'] as &$v){
                                $v['location'] = $this->parseSarif($v['location']);
                            }
                        }
                    }
                }
            }
        }

        $info['locations'] = $this->parseSarif(json_decode($info['locations'], true) ?: []);
        $info['prompt'] = isset($info['prompt']) ? str_replace("\n","<br>",$info['prompt']) : '';


        return redirect('/web/');
    }

    private function parseSarif($list)
    {
        if (!is_array($list) || empty($list)) {
            return [];
        }

        $results = [];
        foreach ($list as $location) {
            if (!is_array($location)) {
                continue;
            }
            if(!isset($location['physicalLocation']))  $location['physicalLocation'] = $location;

            if(!isset($location['physicalLocation']['artifactLocation']['uri'])) continue;
            $artifactLocation = $location['physicalLocation']['artifactLocation']['uri'];
            $region = $location['physicalLocation']['region'] ?? [];
            $startLine = $region['startLine'] ?? 0;
            $startColumn = $region['startColumn'] ?? 0;
            $endColumn = $region['endColumn'] ?? 0;
            $results[] = [
                'file' => $artifactLocation,
                'start_line' => $startLine,
                'start_column' => $startColumn,
                'end_column' => $endColumn
            ];


        }

        return $results;
    }

    public function readFile(Request $request)
    {
        $filePath = $request->param('file');


        if (!isset($filePath)) {
            http_response_code(400);
            echo json_encode(['error' => 'No file specified']);
            exit;
        }


        // Ensure the file path is safe
        $realBase = '/data/code/kcweb/';
        $realUserPath = realpath($realBase . $filePath);

        // Check for directory traversal attempts
        if ($realUserPath === false || strpos($realUserPath, $realBase) !== 0) {

            http_response_code(400);
            echo json_encode(['error' => 'Invalid file path']);
            exit;
        }

        // Check if the file exists and is readable
        if (!file_exists($realUserPath) || !is_readable($realUserPath)) {
            http_response_code(404);
            echo json_encode(['error' => 'File not found or not readable']);
            exit;
        }

        // Return file content
        $content = file_get_contents($realUserPath);
        $encodedContent = base64_encode($content);
        return json(['content' => $encodedContent]);

    }
}
