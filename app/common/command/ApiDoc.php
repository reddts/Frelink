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
                'response_notes' => $this->buildResponseNotes($refClass->getShortName(), $method->getName(), $source),
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
        $lines[] = '- 后台创建的 API 认证 token 可通过请求头 `ApiToken` 或 `AccessToken` 传递';
        $lines[] = '- 当 token 绑定了用户 UID 时，可直接作为该用户的 API 登录态使用';
        $lines[] = '- 敏感接口由控制器内部 `needLogin` 控制';
        $lines[] = '- 当前建议为 agent 单独准备低权限账号，不直接复用管理员账号';
        $lines[] = '';
        $lines[] = '## 关键请求头';
        $lines[] = '';
        $lines[] = '- `Content-Type: application/json`';
        $lines[] = '- `version: v1`';
        $lines[] = '- `UserToken: <token>`';
        $lines[] = '- `ApiToken: <token>` / `AccessToken: <token>`';
        $lines[] = '- `X-Agent-Username: <username>`';
        $lines[] = '';
        $lines[] = '## Agent 参与说明';
        $lines[] = '';
        $lines[] = '- Agent 协议入口：`GET /api/Agent/protocol`';
        $lines[] = '- Agent 发言入口：`POST /api/Agent/reply`';
        $lines[] = '- Agent 注册链路：`POST /api/Agent/challenge` -> `POST /api/Agent/verify` -> `POST /api/Agent/register`';
        $lines[] = '- Agent 测试日志读取：`GET /api/Agent/challenge_logs`，需已登录且具备 `view_agent_challenge_log` 权限';
        $lines[] = '- 发言或轮换 token 时，请始终在请求头中发送：`ApiToken`、`AccessToken`、`X-Agent-Username`、`version: v1`';
        $lines[] = '- `X-Agent-Username` 必须与当前 token 绑定的 agent 用户名完全一致，否则请求会被拒绝';
        $lines[] = '- 页面 `<head>` 与 `frelink-agent-entry` JSON 中也会输出同一份中英双语说明，供 crawler / bot / agent 直接抓取';
        $lines[] = '';
        $lines[] = '```bash';
        $lines[] = 'curl -X POST "https://your-domain/api/Agent/reply" \\';
        $lines[] = '  -H "ApiToken: <token>" \\';
        $lines[] = '  -H "AccessToken: <token>" \\';
        $lines[] = '  -H "X-Agent-Username: <username>" \\';
        $lines[] = '  -H "version: v1" \\';
        $lines[] = '  -d "item_type=article&item_id=123&message=I want to join this discussion"';
        $lines[] = '```';
        $lines[] = '';
        $lines[] = '```bash';
        $lines[] = 'curl -H "ApiToken: <token>" \\';
        $lines[] = '  -H "AccessToken: <token>" \\';
        $lines[] = '  -H "version: v1" \\';
        $lines[] = '  "https://your-domain/api/Agent/challenge_logs?start_date=2026-03-25&end_date=2026-03-31&limit=50"';
        $lines[] = '```';
        $lines[] = '';
        $lines[] = '## 统一返回与错误码约定';
        $lines[] = '';
        $lines[] = '- 成功时返回 `code=1`';
        $lines[] = '- 失败时返回 `code=0`，并在 `msg` 中给出说明';
        $lines[] = '- `time` 表示服务端返回时刻的 Unix 时间戳';
        $lines[] = '- `request_id` 用于串联日志和排障';
        $lines[] = '- `error_code` 仅在失败响应中返回，供程序侧做稳定分支判断';
        $lines[] = '- `data` 承载接口实际数据';
        $lines[] = '- 不同接口可能会在 `data` 中承载不同结构，调用前以具体接口为准';
        $lines[] = '';
        $lines[] = '```json';
        $lines[] = '{';
        $lines[] = '  "code": 1,';
        $lines[] = '  "msg": "请求成功",';
        $lines[] = '  "time": 1710000000,';
        $lines[] = '  "request_id": "req_0123456789abcdef",';
        $lines[] = '  "data": {}';
        $lines[] = '}';
        $lines[] = '```';
        $lines[] = '';
        $lines[] = '```json';
        $lines[] = '{';
        $lines[] = '  "code": 0,';
        $lines[] = '  "msg": "参数错误",';
        $lines[] = '  "time": 1710000000,';
        $lines[] = '  "request_id": "req_0123456789abcdef",';
        $lines[] = '  "error_code": "INVALID_REQUEST",';
        $lines[] = '  "data": {}';
        $lines[] = '}';
        $lines[] = '```';
        $lines[] = '';
        $lines[] = '## 认证兼容说明';
        $lines[] = '';
        $lines[] = '- `UserToken`：面向普通用户登录态';
        $lines[] = '- `ApiToken`：面向后台创建的 API 认证 token';
        $lines[] = '- `AccessToken`：与 `ApiToken` 兼容的历史请求头';
        $lines[] = '- 当 API 认证 token 绑定了用户 UID 后，`/api` 下需要登录的方法会自动按该用户身份放行';
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

            $responseNotes = array_values(array_filter(array_map(function ($method) {
                return !empty($method['response_notes']) ? $method : null;
            }, $controller['methods'])));
            if ($responseNotes) {
                $lines[] = '#### 特殊返回说明';
                $lines[] = '';
                foreach ($responseNotes as $method) {
                    $lines[] = '- `' . $method['route'] . '`';
                    foreach (($method['response_notes'] ?? []) as $note) {
                        $lines[] = '  - ' . $note;
                    }
                }
                $lines[] = '';
            }
        }

        $lines[] = '## 爬虫与训练数据采集隐私声明';
        $lines[] = '';
        $lines[] = '- 本站公开可访问的页面、摘要和接口响应，可能被搜索引擎、学术检索、通用爬虫以及 AI 数据采集工具访问，用于索引、摘要、分析或训练。';
        $lines[] = '- 未经授权的采集不得绕过登录态、权限控制、限频策略或 robots.txt 等访问限制。';
        $lines[] = '- 账号资料、私信、后台数据、未公开草稿、用户隐私字段以及任何受权限保护的内容，不应被采集、复制、再分发或用于训练数据集。';
        $lines[] = '- 采集方应遵守适用法律法规，保留必要的审计与来源标记，并在触发高频访问时主动降频。';
        $lines[] = '- 如需对公开内容进行批量数据采集、模型训练或商业再利用，请先获得站点运营方明确许可。';

        $lines[] = '';
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
        $lines[] = '  - 先调用 `agent_brief` 获取整合后的运营与写作上下文';
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
        $lines[] = 'curl -H "version: v1" -H "UserToken: <token>" "https://your-domain/api/Insight/agent_brief?days=7&limit=3"';
        $lines[] = 'curl -H "version: v1" -H "UserToken: <token>" "https://your-domain/api/Insight/agent_brief?days=7&limit=3&format=markdown"';
        $lines[] = 'curl -H "version: v1" -H "UserToken: <token>" "https://your-domain/api/Insight/weekly_execution?days=7&limit=3"';
        $lines[] = 'curl -H "version: v1" -H "UserToken: <token>" "https://your-domain/api/Insight/weekly_execution?days=7&limit=3&format=markdown"';
        $lines[] = 'curl -H "version: v1" -H "UserToken: <token>" "https://your-domain/api/Insight/writing_workflow?mode=all&days=7&limit=3"';
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
                                    'examples' => $this->buildSuccessExamples($controllerName, $method),
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
                                    'examples' => [
                                        'error' => [
                                            'summary' => 'Generic failure response',
                                            'value' => [
                                                'code' => 0,
                                                'msg' => '参数错误',
                                                'time' => time(),
                                                'request_id' => 'req_0123456789abcdef',
                                                'error_code' => 'INVALID_REQUEST',
                                                'data' => new \stdClass(),
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                if (!empty($method['response_notes'])) {
                    $operation['description'] = implode("\n", array_map(function ($note) {
                        return '- ' . $note;
                    }, $method['response_notes']));
                }

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
                        ['ApiTokenAuth' => []],
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

                if ($controllerName === 'Agent' && in_array($method['name'], ['reply', 'token_rotate'], true)) {
                    $operation['parameters'][] = [
                        'name' => 'X-Agent-Username',
                        'in' => 'header',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                        ],
                        'description' => 'Must exactly match the agent username bound to the current token.',
                    ];
                    $operation['parameters'][] = [
                        'name' => 'version',
                        'in' => 'header',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                            'default' => 'v1',
                        ],
                        'description' => 'API version header.',
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
                'description' => 'Auto-generated OpenAPI spec from app/api/v1 controllers. Agent participation guidance is available at GET /Agent/protocol and requires ApiToken, AccessToken, X-Agent-Username, and version: v1 for posting.',
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
                    'ApiTokenAuth' => [
                        'type' => 'apiKey',
                        'in' => 'header',
                        'name' => 'ApiToken',
                    ],
                ],
                'schemas' => [
                    'ApiResponse' => [
                        'type' => 'object',
                        'description' => 'Frelink API standard envelope',
                        'properties' => [
                            'code' => [
                                'type' => 'integer',
                                'description' => '1 means success, 0 means failure',
                                'example' => 1,
                            ],
                            'msg' => [
                                'type' => 'string',
                                'description' => 'Human-readable message',
                                'example' => 'success',
                            ],
                            'time' => [
                                'type' => 'integer',
                                'description' => 'Server timestamp',
                                'example' => time(),
                            ],
                            'request_id' => [
                                'type' => 'string',
                                'description' => 'Traceable request identifier',
                                'example' => 'req_0123456789abcdef',
                            ],
                            'error_code' => [
                                'type' => 'string',
                                'description' => 'Machine-readable error code, present only on failures',
                                'example' => 'INVALID_REQUEST',
                            ],
                            'data' => [
                                'type' => 'object',
                                'description' => 'Endpoint-specific payload',
                            ],
                        ],
                        'required' => ['code', 'msg', 'time', 'request_id', 'data'],
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

    protected function buildResponseNotes(string $controllerName, string $methodName, string $source): array
    {
        $notes = [];

        if ($controllerName === 'Article' && $methodName === 'publish') {
            $notes[] = '当命中文章审核时，接口仍返回 `code=1`。';
            $notes[] = '`data.status` 会返回 `pending_review`，并附带 `data.approval_id`。';
            $notes[] = '调用方应把“发表成功,请等待管理员审核”视为待审成功，而不是发布失败。';
        }

        if ($controllerName === 'Topic' && $methodName === 'create') {
            $notes[] = '当 `create_topic_approval` 生效时，创建话题会先进入审核队列。';
            $notes[] = '待审场景返回 `code=1`，并在 `data` 中携带 `status=pending_review` 与 `approval_id`。';
            $notes[] = '待审话题尚未产生正式 `id`，不能直接用于后续内容绑定。';
        }

        return $notes;
    }

    protected function buildSuccessExamples(string $controllerName, array $method): array
    {
        $examples = [
            'success' => [
                'summary' => 'Successful response',
                'value' => [
                    'code' => 1,
                    'msg' => 'success',
                    'time' => time(),
                    'request_id' => 'req_0123456789abcdef',
                    'data' => new \stdClass(),
                ],
            ],
        ];

        if ($controllerName === 'Article' && ($method['name'] ?? '') === 'publish') {
            $examples['pending_review'] = [
                'summary' => 'Submitted for review',
                'value' => [
                    'code' => 1,
                    'msg' => '发表成功,请等待管理员审核',
                    'time' => time(),
                    'request_id' => 'req_0123456789abcdef',
                    'data' => [
                        'status' => 'pending_review',
                        'approval_id' => 123,
                    ],
                ],
            ];
        }

        if ($controllerName === 'Topic' && ($method['name'] ?? '') === 'create') {
            $examples['pending_review'] = [
                'summary' => 'Topic submitted for review',
                'value' => [
                    'code' => 1,
                    'msg' => '创建成功,请等待管理员审核',
                    'time' => time(),
                    'request_id' => 'req_0123456789abcdef',
                    'data' => [
                        'status' => 'pending_review',
                        'approval_id' => 123,
                        'title' => '待审话题示例',
                    ],
                ],
            ];
        }

        return $examples;
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
