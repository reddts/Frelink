{if !empty($category_list) && get_setting('enable_category')=='Y'}
<style>
    .category-nav-item-mobile.category-parent-expanded > a {
        color: #0d6efd;
    }
</style>
<div class="px-3 py-2 bg-white" style=" padding:0">
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
                        <div class="card-body">
                            {foreach $v.childs as $child}
                            <a class="mb-0 mr-3 d-inline-block {if $category==$child.id} active c-active{/if}" data-k="{$k}"
                               data-pjax="pageMain" href="{:url($thisRequest,array_merge(['sort'=>$sort,'category_id'=>$child.id], ($thisController=='article' ? ['type'=>request()->param('type','all')] : [])))}">{$child.title}</a>
                            {/foreach}
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
        let $widget = $('.px-3').has('#category-children-box').first(),
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
