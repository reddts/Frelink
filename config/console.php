<?php

return array (
  'commands' =>
  array (
        'task' => 'app\\common\\command\\Task',
        'we' => 'app\\common\\command\\WeCenter',
        'sitemap:build' => 'app\\common\\command\\Sitemap',
        'insight:report' => 'app\\common\\command\\InsightReport',
        'knowledge:bootstrap' => 'app\\common\\command\\KnowledgeMapBootstrap',
        'api:doc' => 'app\\common\\command\\ApiDoc',
        'agent:challenge:test' => 'app\\common\\command\\AgentChallengeTest',
    ),
);
