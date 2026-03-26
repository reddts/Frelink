<?php

namespace app\common\command;

use ReflectionClass;
use ReflectionMethod;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;

class ApiDoc extends Command
{
    protected function configure()
    {
        $this->setName('api:doc')
            ->addOption('source', null, Option::VALUE_REQUIRED, 'API source directory', 'app/api/v1')
            ->addOption('output', null, Option::VALUE_REQUIRED, 'Output file', 'docs/api-v1.md')
            ->addOption('format', null, Option::VALUE_REQUIRED, 'Output format: markdown or openapi', 'markdown')
            ->setDescription('Generate Frelink API v1 docs from controller source');
    }

    protected function execute(Input $input, Output $output)
    {
        $rootPath = rtrim((string) $this->app->getRootPath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $format = strtolower(trim((string) $input->getOption('format')));
        if (!in_array($format, ['markdown', 'openapi'], true)) {
            $output->error('Invalid format. Use --format=markdown or --format=openapi');
            return 1;
        }

        $sourceDir = $this->resolvePath($rootPath, (string) $input->getOption('source'));
        $outputOption = trim((string) $input->getOption('output'));
        $defaultOutput = $format === 'openapi'
            ? 'public/docs/api-v1.openapi.json'
            : 'docs/api-v1.md';
        $outputFile = $this->resolvePath($rootPath, $outputOption !== '' ? $outputOption : $defaultOutput);
        if ($format === 'openapi' && $outputOption === 'docs/api-v1.md') {
            $outputFile = $this->resolvePath($rootPath, $defaultOutput);
        }

        if (!is_dir($sourceDir)) {
            $output->error('Source directory not found: ' . $sourceDir);
            return 1;
        }

        $controllers = $this->scanControllers($sourceDir);
        if (!$controllers) {
            $output->error('No API controllers found in ' . $sourceDir);
            return 1;
        }

        $content = $format === 'openapi'
            ? json_encode($this->buildOpenApi($controllers), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            : $this->buildMarkdown($controllers);
        if ($content === false) {
            $output->error('Failed to encode OpenAPI JSON');
            return 1;
        }

        $dir = dirname($outputFile);
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            $output->error('Failed to create output directory: ' . $dir);
            return 1;
        }

        file_put_contents($outputFile, $content);
        $output->info('API docs generated successfully');
        $output->writeln('Format: ' . $format);
        $output->writeln('File: ' . $outputFile);
        $output->writeln('Controllers: ' . count($controllers));
        $output->writeln('Endpoints: ' . array_sum(array_map(function ($item) {
            return count($item['methods']);
        }, $controllers)));
        return 0;
    }

    protected function scanControllers(string $sourceDir): array
    {
        $controllers = [];
        foreach (glob(rtrim($sourceDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.php') ?: [] as $file) {
            $className = 'app\\api\\v1\\' . pathinfo($file, PATHINFO_FILENAME);
            if (!class_exists($className)) {
                continue;
            }

            $refClass = new ReflectionClass($className);
            if ($refClass->isAbstract()) {
                continue;
            }

            $defaultProps = $refClass->getDefaultProperties();
            $needLogin = array_values(array_filter(array_map('strval', (array) ($defaultProps['needLogin'] ?? []))));
            $controllers[] = [
                'class' => $className,
                'name' => $refClass->getShortName(),
                'file' => $file,
                'need_login' => $needLogin,
                'methods' => $this->scanMethods($refClass, $needLogin),
            ];
        }

        usort($controllers, function ($a, $b) {
            return strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
        });

        return $controllers;
    }

    protected function scanMethods(ReflectionClass $refClass, array $needLogin): array
    {
        $methods = [];
        foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getDeclaringClass()->getName() !== $refClass->getName()) {
                continue;
            }
            if (in_array($method->getName(), ['configure', 'execute'], true)) {
                continue;
            }

            $source = $this->getMethodSource($method);
            $methods[] = [
                'name' => $method->getName(),
                'route' => '/api/' . $refClass->getShortName() . '/' . $method->getName(),
                'http' => $this->guessHttpMethod($method->getName(), $source),
                'login' => in_array('*', $needLogin, true) || in_array($method->getName(), $needLogin, true) ? '需要登录' : '公开',
                'login_required' => in_array('*', $needLogin, true) || in_array($method->getName(), $needLogin, true),
                'params' => $this->extractParams($source),
                'param_meta' => $this->extractParamMeta($source),
                'summary' => $this->buildSummary($method->getName(), $source),
            ];
        }

        usort($methods, function ($a, $b) {
            return strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
        });

        return $methods;
    }

    protected function buildMarkdown(array $controllers): string
    {
        $lines = [];
        $lines[] = '# Frelink API v1';
        $lines[] = '';
        $lines[] = '自动生成时间：' . date('Y-m-d H:i:s');
        $lines[] = '生成命令：`php think api:doc --output docs/api-v1.md`';
        $lines[] = '';
        $lines[] = 'Frelink 的 API 以 `app/api/v1` 为入口，当前更适合作为移动端、站点集成层和 agent 辅助能力的基础接口，而不是直接无约束开放的自动化控制面。';
        $lines[] = '';
        $lines[] = '## 路由规则';
        $lines[] = '';
        $lines[] = '- 默认路由格式：`/api/{controller}/{function}`';
        $lines[] = '- 版本通过请求头 `version` 指定，默认值为 `v1`';
        $lines[] = '- 例如：`GET /api/Common/config`';
        $lines[] = '';
        $lines[] = '## 认证方式';
        $lines[] = '';
        $lines[] = '- 登录态通过请求头 `UserToken` 传递';
        $lines[] = '- 敏感接口由控制器内部 `needLogin` 控制';
        $lines[] = '- 当前建议为 agent 单独准备低权限账号，不直接复用管理员账号';
        $lines[] = '';
        $lines[] = '## 关键请求头';
        $lines[] = '';
        $lines[] = '- `Content-Type: application/json`';
        $lines[] = '- `version: v1`';
        $lines[] = '- `UserToken: <token>`';
        $lines[] = '';
        $lines[] = '## 通用返回结构';
        $lines[] = '';
        $lines[] = '```json';
        $lines[] = '{';
        $lines[] = '  "code": 0,';
        $lines[] = '  "msg": "",';
        $lines[] = '  "time": 1710000000,';
        $lines[] = '  "data": {}';
        $lines[] = '}';
        $lines[] = '```';
        $lines[] = '';
        $lines[] = '## 接口清单';
        $lines[] = '';

        foreach ($controllers as $controller) {
            $lines[] = '### `' . $controller['name'] . '`';
            $lines[] = '';
            if (!empty($controller['need_login'])) {
                $needLogin = in_array('*', $controller['need_login'], true)
                    ? '`*`'
                    : '`' . implode('`, `', $controller['need_login']) . '`';
                $lines[] = '- 需要登录的方法：' . $needLogin;
                $lines[] = '';
            }
            $lines[] = '| 方法 | 路由 | HTTP | 鉴权 | 参数 | 说明 |';
            $lines[] = '| --- | --- | --- | --- | --- | --- |';
            foreach ($controller['methods'] as $method) {
                $params = $method['params'] ? '`' . implode('`, `', $method['params']) . '`' : '-';
                $summary = $this->escapeTableCell($method['summary']);
                $lines[] = '| `' . $method['name'] . '` | `' . $method['route'] . '` | `' . $method['http'] . '` | ' . $method['login'] . ' | ' . $params . ' | ' . $summary . ' |';
            }
            $lines[] = '';
        }

        $lines[] = '## OpenAPI 导出';
        $lines[] = '';
        $lines[] = '- 机器可读规范默认输出到 `public/docs/api-v1.openapi.json`';
        $lines[] = '- 浏览器可直接访问 `https://your-domain/docs/api-v1.openapi.json`';
        $lines[] = '- 生成命令：`php think api:doc --format=openapi --output public/docs/api-v1.openapi.json`';
        $lines[] = '';

        $lines[] = '## 推荐的 agent 使用边界';
        $lines[] = '';
        $lines[] = '- 允许：';
        $lines[] = '  - 搜索词采集与选题建议';
        $lines[] = '  - 内容健康巡检';
        $lines[] = '  - 发布后链接检查';
        $lines[] = '  - 收录状态巡检';
        $lines[] = '  - 低风险草稿生成';
        $lines[] = '- 不建议直接开放：';
        $lines[] = '  - 删除内容';
        $lines[] = '  - 修改权限';
        $lines[] = '  - 自动正式发布';
        $lines[] = '  - 无审批的生产运维操作';
        $lines[] = '';
        $lines[] = '## 接入前建议';
        $lines[] = '';
        $lines[] = '1. 为 agent 单独创建账号与权限组。';
        $lines[] = '2. 先补齐接口 smoke test，再接入自动化发布流程。';
        $lines[] = '3. 为发布、删除、推荐、置顶等动作记录审计日志。';
        $lines[] = '4. 对登录、短信、发布、评论等接口增加限频与监控。';
        $lines[] = '';
        $lines[] = '## smoke test 建议';
        $lines[] = '';
        $lines[] = '```bash';
        $lines[] = 'curl -H "version: v1" https://your-domain/api/Common/config';
        $lines[] = 'curl -H "version: v1" "https://your-domain/api/Common/search?q=frelink"';
        $lines[] = 'curl -H "version: v1" "https://your-domain/api/Question/index?page=1&page_size=5"';
        $lines[] = 'curl -H "version: v1" "https://your-domain/api/Article/index?page=1&page_size=5"';
        $lines[] = 'curl -H "version: v1" -H "UserToken: <token>" "https://your-domain/api/Insight/weekly_execution?days=7&limit=3"';
        $lines[] = 'curl -H "version: v1" -H "UserToken: <token>" "https://your-domain/api/Insight/weekly_execution?days=7&limit=3&format=markdown"';
        $lines[] = 'curl -X POST -H "version: v1" -H "Content-Type: application/json" \\';
        $lines[] = '  -d \'{"event_type":"detail_view","item_type":"article","item_id":1,"visitor_token":"debug-token","source":"smoke_test"}\' \\';
        $lines[] = '  https://your-domain/api/Insight/track';
        $lines[] = '```';
        $lines[] = '';
        $lines[] = '## 后续改造方向';
        $lines[] = '';
        $lines[] = '- 增加 OpenAPI 文档';
        $lines[] = '- 统一错误码规范';
        $lines[] = '- 为 agent 提供只读巡检 token';
        $lines[] = '- 为发布流程增加审批与回滚机制';
        $lines[] = '- 为 Insight 增加后台面板与定时报表';

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    protected function buildOpenApi(array $controllers): array
    {
        $paths = [];
        foreach ($controllers as $controller) {
            $controllerName = (string) ($controller['name'] ?? '');
            foreach (($controller['methods'] ?? []) as $method) {
                $path = '/' . $controllerName . '/' . $method['name'];
                $httpMethod = strtolower((string) ($method['http'] ?? 'get'));
                $operation = [
                    'tags' => [$controllerName],
                    'summary' => (string) ($method['summary'] ?? ''),
                    'operationId' => $controllerName . '_' . $method['name'],
                    'parameters' => [],
                    'responses' => [
                        '200' => [
                            'description' => 'Success',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/ApiResponse',
                                    ],
                                ],
                            ],
                        ],
                        '400' => [
                            'description' => 'Bad Request',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/ApiResponse',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                foreach (($method['param_meta'] ?? []) as $paramMeta) {
                    $parameter = [
                        'name' => (string) ($paramMeta['name'] ?? ''),
                        'in' => $httpMethod === 'get' ? 'query' : 'query',
                        'required' => (bool) ($paramMeta['required'] ?? false),
                        'schema' => [
                            'type' => (string) ($paramMeta['type'] ?? 'string'),
                        ],
                    ];
                    $operation['parameters'][] = $parameter;
                }

                if (!empty($method['login_required'])) {
                    $operation['security'] = [
                        ['UserTokenAuth' => []],
                    ];
                }

                if ($httpMethod === 'post') {
                    $properties = [];
                    $required = [];
                    foreach (($method['param_meta'] ?? []) as $paramMeta) {
                        $name = (string) ($paramMeta['name'] ?? '');
                        if ($name === '') {
                            continue;
                        }
                        $properties[$name] = [
                            'type' => (string) ($paramMeta['type'] ?? 'string'),
                        ];
                        if (!empty($paramMeta['required'])) {
                            $required[] = $name;
                        }
                    }

                    $operation['requestBody'] = [
                        'required' => !empty($required),
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => $properties,
                                    'required' => $required,
                                    'additionalProperties' => true,
                                ],
                            ],
                        ],
                    ];
                }

                $paths[$path][$httpMethod] = $operation;
            }
        }

        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'Frelink API v1',
                'version' => 'v1',
                'description' => 'Auto-generated OpenAPI spec from app/api/v1 controllers.',
            ],
            'servers' => [
                [
                    'url' => '/api',
                ],
            ],
            'tags' => array_values(array_map(function ($controller) {
                return [
                    'name' => (string) ($controller['name'] ?? ''),
                ];
            }, $controllers)),
            'paths' => $paths,
            'components' => [
                'securitySchemes' => [
                    'UserTokenAuth' => [
                        'type' => 'apiKey',
                        'in' => 'header',
                        'name' => 'UserToken',
                    ],
                ],
                'schemas' => [
                    'ApiResponse' => [
                        'type' => 'object',
                        'properties' => [
                            'code' => [
                                'type' => 'integer',
                            ],
                            'msg' => [
                                'type' => 'string',
                            ],
                            'time' => [
                                'type' => 'integer',
                            ],
                            'data' => [
                                'description' => 'Endpoint-specific payload',
                            ],
                        ],
                        'required' => ['code', 'msg', 'time', 'data'],
                    ],
                ],
            ],
        ];
    }

    protected function getMethodSource(ReflectionMethod $method): string
    {
        $file = $method->getFileName();
        if (!$file || !is_file($file)) {
            return '';
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES);
        if (!$lines) {
            return '';
        }

        $start = max(0, $method->getStartLine() - 1);
        $end = max($start, $method->getEndLine() - 1);
        $chunk = array_slice($lines, $start, $end - $start + 1);
        return implode("\n", $chunk);
    }

    protected function extractParams(string $source): array
    {
        $meta = $this->extractParamMeta($source);
        $params = array_map(function ($item) {
            return (string) ($item['name'] ?? '');
        }, $meta);
        $params = array_values(array_unique(array_filter(array_map('trim', $params))));
        sort($params);
        return $params;
    }

    protected function extractParamMeta(string $source): array
    {
        if ($source === '') {
            return [];
        }

        $meta = [];
        preg_match_all('/(?:\$this->)?request(?:\(\))?->(?:param|get|post)\(\s*[\'"]([^\'"]+)[\'"](?:\s*,\s*([^,\)]+))?/u', $source, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $name = trim((string) ($match[1] ?? ''));
            if ($name === '') {
                continue;
            }

            $default = isset($match[2]) ? trim((string) $match[2]) : '';
            $required = $default === '';
            if ($default !== '') {
                $required = !preg_match('/^(null|NULL)$/', $default);
            }

            $meta[$name] = [
                'name' => $name,
                'required' => $required,
                'type' => $this->inferParameterType($name, $default),
            ];
        }

        return array_values($meta);
    }

    protected function inferParameterType(string $name, string $default = ''): string
    {
        $lowerName = strtolower($name);
        $default = trim($default);

        if ($default !== '' && preg_match('/^\d+$/', $default)) {
            return 'integer';
        }

        if ($default !== '' && preg_match('/^(true|false|0|1)$/i', $default)) {
            return 'boolean';
        }

        if (preg_match('/^(id|uid|page|page_size|per_page|limit|sort|status|type|pid|cid|tid|rid|answer_id|question_id|article_id|item_id|chapter_id|reward_id|column_id|days|count)$/', $lowerName)) {
            return 'integer';
        }

        if (preg_match('/^(is_|has_|enable|enabled|visible|public|checked|active|status)$/', $lowerName)) {
            return 'boolean';
        }

        if (preg_match('/(time|date|at|_at$|_time$|start|end|expired|expires)/', $lowerName)) {
            return 'string';
        }

        return 'string';
    }

    protected function guessHttpMethod(string $methodName, string $source): string
    {
        $hasPost = (bool) preg_match('/request->post\\(/', $source);
        $hasGet = (bool) preg_match('/request->get\\(/', $source);
        $hasParam = (bool) preg_match('/request->param\\(/', $source);

        if ($hasPost && !$hasGet && !$hasParam) {
            return 'POST';
        }
        if (($hasGet || $hasParam) && !$hasPost) {
            return 'GET';
        }

        if ($hasPost) {
            return 'POST';
        }

        if (preg_match('/^(publish|track|save|update|remove|set|upload|send|post|delete)/i', $methodName)) {
            return 'POST';
        }

        return 'GET';
    }

    protected function buildSummary(string $methodName, string $source): string
    {
        $map = [
            'track' => '记录曝光、点击或详情阅读事件',
            'summary' => '返回最近窗口运营汇总',
            'keywords' => '返回最近窗口高频搜索词',
            'opportunities' => '返回搜索缺口与内容建议',
            'content_trends' => '返回内容曝光、点击和阅读趋势',
            'topic_trends' => '返回主题趋势',
            'recommendations' => '返回运营建议动作',
            'publish_assist' => '返回发布选题与标题建议',
            'weekly_execution' => '返回本周执行清单',
            'config' => '返回公开配置',
            'search' => '执行站内搜索',
            'category' => '返回分类列表',
            'index' => '返回列表数据',
            'detail' => '返回详情数据',
            'publish' => '发布或修改内容',
            'comments' => '返回评论列表',
            'relation' => '返回相关文章',
        ];

        if (isset($map[$methodName])) {
            return $map[$methodName];
        }

        $params = $this->extractParams($source);
        $summary = '自动生成接口说明';
        if ($params) {
            $summary .= '，参数：' . implode(', ', $params);
        }
        return $summary;
    }

    protected function escapeTableCell(string $value): string
    {
        return str_replace(['|', "\n", "\r"], ['\\|', ' ', ' '], $value);
    }

    protected function resolvePath(string $rootPath, string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return $rootPath;
        }

        if (preg_match('/^(\/|[A-Za-z]:[\\\\\/])/', $path)) {
            return $path;
        }

        return $rootPath . ltrim($path, DIRECTORY_SEPARATOR . '/');
    }
}
