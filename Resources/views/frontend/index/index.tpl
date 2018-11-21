
{* file to extend *}
{extends file="parent:frontend/index/index.tpl"}

{* set our namespace *}
{namespace name="frontend/ost-article-search/index/index"}



{* append our javascript *}
{block name='frontend_index_header_javascript_jquery'}

    {* our plugin configuration *}
    <script type="text/javascript">

        {* javascript variables *}
        var ostArticleSearch = {
            searchUrl: '{url controller="OstArticleSearch"}?ostas_search=__search__'
        };

    </script>

    {* smarty parent *}
    {$smarty.block.parent}

{/block}


