
{* file to extend *}
{extends file="parent:frontend/listing/index.tpl"}

{* our plugin namespace *}
{namespace name="frontend/ost-article-search/index"}



{* add our plugin to the breadcrumb *}
{block name='frontend_index_start'}

    {* smarty parent *}
    {$smarty.block.parent}

    {* our breadcrumb name *}
    {assign var="breadcrumbName" value="Artikel Suche"}

    {* add it *}
    {$sBreadcrumb[] = ['name' => $breadcrumbName, 'link'=> ""]}

{/block}



{* remove topseller since we would use every article for the complete shop category *}
{block name="frontend_listing_index_topseller"}
{/block}



{* we need to overwrite default ajax count call *}
{block name="frontend_index_controller_url"}

    {* set ajax url *}
    {$countCtrlUrl = "{url module="widgets" controller="OstArticleSearch" action="listingCount" params=$ajaxCountUrlParams fullPath}"}

{/block}
