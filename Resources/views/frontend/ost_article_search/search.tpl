
{* file to extend *}
{extends file="parent:frontend/index/index.tpl"}

{* our plugin namespace *}
{namespace name="frontend/ost-article-search/search"}



{* remove left sidebar *}
{block name='frontend_index_content_left'}{/block}



{* main content *}
{block name='frontend_index_content'}

    <div class="content content--ost-article-search">
        <h2 class="panel--title is--underline">
            SCHNELL JEDEN ARTIKEL FINDEN
        </h2>

        <form>
            <div style="float: left; width: calc(50% - 10px); margin-right: 20px;">
                <input name="article_number" type="text" placeholder="Artikelnummer..." />
                <input name="article_type" type="text" placeholder="Typ..." />
                <input name="article_supplier" type="text" placeholder="Hersteller..." />
                <input name="article_model" type="text" placeholder="Modell..." />
            </div>
            <div style="float: right; width: calc(50% - 10px); text-align: right;">
                <input name="article_color" type="text" placeholder="Farbe..." />
                <input name="price" class="price" type="text" placeholder="Maximaler Preis..." />
            </div>
            <div style="clear: both;"></div>
            <div style="text-align: center; margin-top: 12px;">
                <button class="btn is--primary">Artikel finden</button>
            </div>
        </form>
    </div>

{/block}
