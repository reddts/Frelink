<?php

return array (
  'home' => 
  array (
    'title' => '发现页',
    'config' => 
    array (
      'search_enable' => 
      array (
        'title' => '首页搜索',
        'type' => 'radio',
        'value' => 'Y',
        'options' => 
        array (
          'N' => '不开启',
          'Y' => '开启',
        ),
        'tips' => '',
      ),
      'search_bg' => 
      array (
        'title' => '头部背景图',
        'type' => 'image',
        'value' => '',
        'options' => 
        array (
        ),
        'tips' => '',
      ),
      'search_title_text' => 
      array (
        'title' => '搜索标题文字',
        'type' => 'text',
        'value' => '公开、开放、可检索的知识站',
        'options' => 
        array (
        ),
        'tips' => '',
      ),
      'search_min_text' => 
      array (
        'title' => '搜索介绍文字',
        'type' => 'textarea',
        'value' => '围绕问题、文章、主题与专题持续沉淀知识，不做诱导付费，不做碎片化阅读。',
        'options' => 
        array (
        ),
        'tips' => '',
      ),
      'sidebar_show_items' => 
      array (
        'title' => '侧边栏项目',
        'type' => 'checkbox',
        'value' => 'announce',
        'options' => 
        array (
          'write_nav' => '快速发起',
          'announce' => '网站公告',
          'focus_topic' => '关注话题',
          'column' => '热门专栏',
          'hot_topic' => '热门话题',
          'hot_users' => '热门用户',
          'diy_content' => '自定义内容',
        ),
        'tips' => '侧边栏显示的项目',
      ),
      'sidebar_diy_content' => 
      array (
        'title' => '自定义内容',
        'type' => 'editor',
        'value' => '',
        'options' => 
        array (
        ),
        'tips' => '侧边栏自定义内容,支持富文本',
      ),
      'links_show_type' => 
      array (
        'title' => '友情链接展示方式',
        'type' => 'radio',
        'value' => 'text',
        'options' => 
        array (
          'text' => '文字链接',
          'image' => '图片链接',
        ),
        'tips' => '侧边栏自定义内容,支持富文本',
      ),
    ),
  ),
  'question' => 
  array (
    'title' => '问答页面',
    'config' => 
    array (
      'sidebar_show_items' => 
      array (
        'title' => '侧边栏项目',
        'type' => 'checkbox',
        'value' => 'write_nav,announce,hot_topic,column,diy_content',
        'options' => 
        array (
          'write_nav' => '快速发起',
          'announce' => '网站公告',
          'focus_topic' => '关注话题',
          'column' => '热门专栏',
          'hot_topic' => '热门话题',
          'hot_users' => '热门用户',
          'diy_content' => '自定义内容',
        ),
        'tips' => '侧边栏显示的项目',
      ),
      'sidebar_diy_content' => 
      array (
        'title' => '自定义内容',
        'type' => 'editor',
        'value' => '&lt;div class=&quot;p-3 mt-2&quot; style=&quot;background:#f8fafc;border:1px solid #e5e7eb;border-radius:12px;&quot;&gt;优先回答有明确搜索意图的问题，并把高质量回答沉淀成文章或帮助文档。&lt;/div&gt;',
        'options' => 
        array (
        ),
        'tips' => '侧边栏自定义内容,支持富文本',
      ),
    ),
  ),
  'article' => 
  array (
    'title' => '文章页面',
    'config' => 
    array (
      'sidebar_show_items' => 
      array (
        'title' => '侧边栏项目',
        'type' => 'checkbox',
        'value' => 'write_nav,announce,hot_topic,column,diy_content',
        'options' => 
        array (
          'write_nav' => '快速发起',
          'announce' => '网站公告',
          'focus_topic' => '关注话题',
          'column' => '热门专栏',
          'hot_topic' => '热门话题',
          'hot_users' => '热门用户',
          'diy_content' => '自定义内容',
        ),
        'tips' => '侧边栏显示的项目',
      ),
      'sidebar_diy_content' => 
      array (
        'title' => '自定义内容',
        'type' => 'editor',
        'value' => '&lt;div class=&quot;p-3 mt-2&quot; style=&quot;background:#f8fafc;border:1px solid #e5e7eb;border-radius:12px;&quot;&gt;文章优先写成可复用的教程、案例、方案对比和实践总结，而不是纯时间流内容。&lt;/div&gt;',
        'options' => 
        array (
        ),
        'tips' => '侧边栏自定义内容,支持富文本',
      ),
    ),
  ),
  'column' => 
  array (
    'title' => '专栏页',
    'config' => 
    array (
      'navbar_bg' => 
      array (
        'title' => '头部背景图',
        'type' => 'image',
        'value' => '',
        'options' => 
        array (
        ),
        'tips' => '',
      ),
      'navbar_text' => 
      array (
        'title' => '标题文字',
        'type' => 'text',
        'value' => '',
        'options' => 
        array (
        ),
        'tips' => '',
      ),
    ),
  ),
  'common' => 
  array (
    'title' => '通用设置',
    'config' => 
    array (
      'bg_logo' => 
      array (
        'title' => '背景图logo',
        'type' => 'image',
        'value' => '',
        'options' => 
        array (
        ),
        'tips' => '导航带背景时的logo',
      ),
      'list_show_image' => 
      array (
        'title' => '列表显示图片',
        'type' => 'radio',
        'value' => 'Y',
        'options' => 
        array (
          'N' => '不显示',
          'Y' => '显示',
        ),
        'tips' => '',
      ),
      'fixed_navbar' => 
      array (
        'title' => '固定导航',
        'type' => 'radio',
        'value' => 'N',
        'options' => 
        array (
          'N' => '不固定',
          'Y' => '固定',
        ),
        'tips' => '',
      ),
      'login_type' => 
      array (
        'title' => '登录方式',
        'type' => 'radio',
        'value' => 'dialog',
        'options' => 
        array (
          'page' => '新页面',
          'dialog' => '弹窗',
        ),
        'tips' => '',
      ),
      'enable_mathjax' => 
      array (
        'title' => '启用Mathjax支持',
        'type' => 'radio',
        'value' => 'N',
        'options' => 
        array (
          'N' => '不支持',
          'Y' => '支持',
        ),
        'tips' => '',
      ),
      'filter-grey' => 
      array (
        'title' => '网站置灰',
        'type' => 'radio',
        'value' => 'N',
        'options' => 
        array (
          'N' => '不启用',
          'Y' => '启用',
        ),
        'tips' => '',
      ),
    ),
  ),
  'question_detail' => 
  array (
    'title' => '问题详情',
    'config' => 
    array (
      'sidebar_show_relation_question' => 
      array (
        'title' => '侧边栏显示相关问题',
        'type' => 'radio',
        'value' => 'Y',
        'options' => 
        array (
          'N' => '不显示',
          'Y' => '显示',
        ),
        'tips' => '侧边栏显示相关问题',
      ),
      'sidebar_show_recommend_post' => 
      array (
        'title' => '侧边栏显示推荐内容',
        'type' => 'radio',
        'value' => 'Y',
        'options' => 
        array (
          'N' => '不显示',
          'Y' => '显示',
        ),
        'tips' => '侧边栏显示推荐内容',
      ),
      'sidebar_show_items' => 
      array (
        'title' => '侧边栏项目',
        'type' => 'checkbox',
        'value' => '',
        'options' => 
        array (
          'write_nav' => '快速发起',
          'announce' => '网站公告',
          'focus_topic' => '关注话题',
          'column' => '热门专栏',
          'hot_topic' => '热门话题',
          'hot_users' => '热门用户',
          'diy_content' => '自定义内容',
        ),
        'tips' => '侧边栏显示的项目',
      ),
      'sidebar_diy_content' => 
      array (
        'title' => '自定义内容',
        'type' => 'editor',
        'value' => '&lt;p class=&quot;p-3 bg-info mt-2&quot;&gt;这是自定义内容&lt;/p&gt;',
        'options' => 
        array (
        ),
        'tips' => '侧边栏自定义内容,支持富文本',
      ),
    ),
  ),
  'article_detail' => 
  array (
    'title' => '文章详情',
    'config' => 
    array (
      'sidebar_show_relation_article' => 
      array (
        'title' => '侧边栏显示相关文章',
        'type' => 'radio',
        'value' => 'Y',
        'options' => 
        array (
          'N' => '不显示',
          'Y' => '显示',
        ),
        'tips' => '侧边栏显示相关文章',
      ),
      'sidebar_show_recommend_post' => 
      array (
        'title' => '侧边栏显示推荐内容',
        'type' => 'radio',
        'value' => 'Y',
        'options' => 
        array (
          'N' => '不显示',
          'Y' => '显示',
        ),
        'tips' => '侧边栏显示推荐内容',
      ),
      'sidebar_show_items' => 
      array (
        'title' => '侧边栏项目',
        'type' => 'checkbox',
        'value' => '',
        'options' => 
        array (
          'write_nav' => '快速发起',
          'announce' => '网站公告',
          'focus_topic' => '关注话题',
          'column' => '热门专栏',
          'hot_topic' => '热门话题',
          'hot_users' => '热门用户',
          'diy_content' => '自定义内容',
        ),
        'tips' => '侧边栏显示的项目',
      ),
      'sidebar_diy_content' => 
      array (
        'title' => '自定义内容',
        'type' => 'editor',
        'value' => '&lt;p class=&quot;p-3 bg-info mt-2&quot;&gt;这是自定义内容&lt;/p&gt;',
        'options' => 
        array (
        ),
        'tips' => '侧边栏自定义内容,支持富文本',
      ),
    ),
  ),
  'mobile' => 
  array (
    'title' => '手机端设置',
    'config' => 
    array (
      'index_hot_banner_enable' => 
      array (
        'title' => '是否启用首页热门',
        'type' => 'radio',
        'value' => 'Y',
        'options' => 
        array (
          'N' => '不开启',
          'Y' => '开启',
        ),
        'tips' => '是否启用首页热门',
      ),
      'index_hot_user_image' => 
      array (
        'title' => '首页热门用户图片',
        'type' => 'image',
        'value' => '/templates/mobile/img/hot-user.png',
        'options' => 
        array (
        ),
        'tips' => '首页热门用户图片',
      ),
      'index_hot_user_url' => 
      array (
        'title' => '首页热门用户URL',
        'type' => 'text',
        'value' => '',
        'options' => 
        array (
        ),
        'tips' => '首页热门用户URL',
      ),
      'index_hot_topic_image' => 
      array (
        'title' => '首页热门话题图片',
        'type' => 'image',
        'value' => '/templates/mobile/img/hot-topic.png',
        'options' => 
        array (
        ),
        'tips' => '首页热门用户图片',
      ),
      'index_hot_topic_url' => 
      array (
        'title' => '首页热门话题URL',
        'type' => 'text',
        'value' => '',
        'options' => 
        array (
        ),
        'tips' => '首页热门用户URL',
      ),
    ),
  ),
);
