{if !empty($category_list) && get_setting('enable_category')=='Y'}
<style>
    .aw-mobile-category-widget {
        padding: 0 !important;
        border-top: 1px solid #eef2f7;
        border-bottom: 1px solid #eef2f7;
        background: #fff;
    }
    .aw-mobile-category-widget .n-tab {
        padding: 0 12px;
    }
    .aw-mobile-category-widget .categoryContainer {
        padding: 10px 0 8px;
    }
    .aw-mobile-category-widget .category-nav-item-mobile {
        width: auto;
        margin-right: 6px;
    }
    .aw-mobile-category-widget .category-nav-item-mobile > a {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #dbe7f3;
        background: #fff;
        color: #60758b;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        transition: all .18s ease;
    }
    .aw-mobile-category-widget .category-nav-item-mobile > a.active,
    .aw-mobile-category-widget .category-nav-item-mobile > a.c-active {
        color: #1d4ed8;
        border-color: #bfdbfe;
        background: #eff6ff;
    }
    .category-nav-item-mobile.category-parent-expanded > a {
        color: #0d6efd;
        border-color: #bfdbfe;
        background: linear-gradient(180deg, #f8fbff 0%, #edf5ff 100%);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.08);
    }
    .aw-mobile-category-widget .category-children-panel {
        margin: 0 12px 12px;
        padding: 12px;
        border: 1px solid #dbe7f3;
        border-radius: 16px;
        background: linear-gradient(180deg, #fbfdff 0%, #f4f8ff 100%);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
    }
    .aw-mobile-category-widget .category-children-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }
    .aw-mobile-category-widget .category-children-title {
        display: inline-flex;
        align-items: center;
        min-width: 0;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.4;
    }
    .aw-mobile-category-widget .category-children-title::before {
        content: "";
        width: 4px;
        height: 14px;
        margin-right: 8px;
        border-radius: 999px;
        background: linear-gradient(180deg, #2563eb 0%, #0ea5e9 100%);
        flex: 0 0 auto;
    }
    .aw-mobile-category-widget .category-children-tip {
        color: #94a3b8;
        font-size: 11px;
        white-space: nowrap;
    }
    .aw-mobile-category-widget .category-children-links {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .aw-mobile-category-widget .category-children-links a {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        border: 1px solid #d9e6f2;
        background: #fff;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
    }
    .aw-mobile-category-widget .category-children-links a.active,
    .aw-mobile-category-widget .category-children-links a.c-active {
        color: #fff;
        border-color: #1d4ed8;
        background: #1d4ed8;
    }
</style>
<div class="px-3 py-2 bg-white aw-mobile-category-widget" style=" padding:0">
    <div class="n-tab align-content-center">
        <div class="swiper-container categoryContainer" style="margin: 0">
            <ul class="n-nav swiper-wrapper" style="flex-wrap: nowrap;">
                <li class="swiper-slide category-nav-item-mobile" data-category-key="0">
                    <a class="mb-0 {if !$category}active c-active{/if} mr-3" data-k="0" data-pjax="pageMain" href="{:url($thisRequest,array_merge(['sort'=>$sort,'category_id'=>0], ($thisController=='article' ? ['type'=>request()->param('type','all')] : [])))}">{:L('全部')}</a>
                </li>
                {foreach $category_list as $k => $v}
                <li class="swiper-slide category-nav-item-mobile" data-category-key="{$k}" data-has-children="{if !empty($v.childs)}1{else/}0{/if}">
                    <a class="{if $category==$v.id} active c-active{/if} mr-3" data-k="{$k}" data-pjax="pageMain" href="{:url($thisRequest,array_merge(['sort'=>$sort,'category_id'=>$v['id']], ($thisController=='article' ? ['type'=>request()->param('type','all')] : [])))}">{$v.title}</a>
                    {if !empty($v.childs)}
                    <div class="card category-children-template" style="display: none">
                        <div class="category-children-panel">
                            <div class="category-children-header">
                                <div class="category-children-title">{$v.title}</div>
                                <div class="category-children-tip">{:L('展开次级入口')}</div>
                            </div>
                            <div class="category-children-links">
                            {foreach $v.childs as $child}
                            <a class="{if $category==$child.id} active c-active{/if}" data-k="{$k}"
                               data-pjax="pageMain" href="{:url($thisRequest,array_merge(['sort'=>$sort,'category_id'=>$child.id], ($thisController=='article' ? ['type'=>request()->param('type','all')] : [])))}">{$child.title}</a>
                            {/foreach}
                            </div>
                        </div>
                    </div>
                    {/if}
                </li>
                {/foreach}
            </ul>
        </div>
    </div>

    <div class="n-tab align-content-center pl-0">
        <div class="card nav-pills border-0" id="category-children-box">
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        let $widget = $('.aw-mobile-category-widget').has('#category-children-box').first(),
            $activeLink = $widget.find('.c-active').first(),
            k = parseInt($activeLink.data('k')) || 0
        new Swiper($widget.find('.categoryContainer')[0], {
            speed: 600,
            grabCursor: true,
            slidesPerView: "auto",
            initialSlide: k,
            slidesPerGroup: 3
        })

        let $childBox = $widget.find('#category-children-box'),
            expandedKey = null

        function isDesktopLike() {
            return window.matchMedia('(hover: hover) and (pointer: fine)').matches
        }

        function setChildren($item) {
            let $template = $item.find('.category-children-template')
            $widget.find('.category-parent-expanded').removeClass('category-parent-expanded')

            if ($template.length) {
                $childBox.html($template.html())
                $item.addClass('category-parent-expanded')
                expandedKey = String($item.data('category-key'))
            } else {
                $childBox.empty()
                expandedKey = null
            }
        }

        let $activeChildCard = $activeLink.closest('.category-children-template')
        if ($activeChildCard.length) {
            setChildren($activeChildCard.closest('.category-nav-item-mobile'))
        } else {
            let $activeItem = $activeLink.closest('.category-nav-item-mobile')
            if (parseInt($activeItem.data('has-children'), 10) === 1) {
                setChildren($activeItem)
            }
        }

        $widget.on('mouseenter', '.category-nav-item-mobile', function () {
            if (!isDesktopLike()) {
                return
            }
            setChildren($(this))
        })

        $widget.on('click', '.category-nav-item-mobile > a', function (e) {
            let $item = $(this).closest('.category-nav-item-mobile'),
                hasChildren = parseInt($item.data('has-children'), 10) === 1,
                itemKey = String($item.data('category-key'))

            if (!hasChildren) {
                if (itemKey === '0') {
                    $childBox.empty()
                }
                return
            }

            if (expandedKey !== itemKey) {
                e.preventDefault()
                setChildren($item)
            }
        })
    })
</script>
{/if}
