
{* file to extend *}
{extends file='parent:frontend/listing/box_article.tpl'}



{* set custom box template when we are in our plugin *}
{block name='frontend_listing_box_article_includes'}
    {if $ostArticleSearchStatus == true}
        {include file="frontend/listing/product-box/box-{$ostArticleSearchBoxTemplate}.tpl" productBoxLayout=$ostArticleSearchBoxTemplate}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
