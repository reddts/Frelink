{if !empty($category_list) && get_setting('enable_category')=='Y'}
<style>
    .swiper-slide {width: auto!important}
    .category-parent-expanded > .nav-link {
        color: #0d6efd;
    }
</style>
<div class="container my-2 " style=" padding:0">
    <div class="row n-tab align-content-center">
        <div class="swiper-container" style="margin: 0">
            <ul class="nav nav-pills n-nav swiper-wrapper" style="flex-wrap: nowrap;">
                <li class="nav-item swiper-slide category-nav-item" data-category-key="0">
                    <a class="nav-link mb-0 {if !$category}active c-active{/if}" data-k="0" data-pjax="wrapMain" href="{:url($thisRequest,array_merge(['sort'=>$sort,'category_id'=>0], ($thisController=='article' ? ['type'=>request()->param('type','all')] : [])))}">{:L('全部分类')}</a>
                </li>
                {foreach $category_list as $k => $v}
                <li class="nav-item swiper-slide category-nav-item" data-category-key="{$k}" data-has-children="{if !empty($v.childs)}1{else/}0{/if}">
                    <a class="nav-link {if $category==$v.id} active c-active{/if}" data-k="{$k}" data-pjax="wrapMain" href="{:url($thisRequest,array_merge(['sort'=>$sort,'category_id'=>$v['id']], ($thisController=='article' ? ['type'=>request()->param('type','all')] : [])))}">{$v.title}</a>
                    {if !empty($v.childs)}
                    <div class="card category-children-template" style="display: none">
                        <div class="card-body">
                            {foreach $v.childs as $child}
                            <a class="nav-link mb-0 d-inline-block {if $category==$child.id} active c-active{/if}" data-k="{$k}"
                               data-pjax="wrapMain" href="{:url($thisRequest,array_merge(['sort'=>$sort,'category_id'=>$child.id], ($thisController=='article' ? ['type'=>request()->param('type','all')] : [])))}">{$child.title}</a>
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
        let $widget = $('.container').has('#category-children-box').first(),
            $activeLink = $widget.find('.c-active').first(),
            k = parseInt($activeLink.data('k')) || 0
        new Swiper($widget.find('.swiper-container')[0], {
            speed: 600,
            grabCursor: true,
            slidesPerView: "auto",
            initialSlide: k,
            slidesPerGroup: 3
        })

        let $childBox = $widget.find('#category-children-box'),
            expandedKey = null

        function isDesktop() {
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
            setChildren($activeChildCard.closest('.category-nav-item'))
        } else {
            let $activeItem = $activeLink.closest('.category-nav-item')
            if (parseInt($activeItem.data('has-children'), 10) === 1) {
                setChildren($activeItem)
            }
        }

        $widget.on('mouseenter', '.category-nav-item', function () {
            if (!isDesktop()) {
                return
            }
            setChildren($(this))
        })

        $widget.on('click', '.category-nav-item > .nav-link', function (e) {
            let $item = $(this).closest('.category-nav-item'),
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
