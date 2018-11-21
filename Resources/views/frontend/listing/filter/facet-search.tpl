
{* facet container *}
<div class="filter-panel filter--value facet--{$facet->getFacetName()|escape:'htmlall'}"
     data-filter-type="value"
     data-field-name="{$facet->getFieldName()|escape:'htmlall'}"
     style="width: 98%;">

    {* inner container*}
    <div class="filter-panel--flyout">

        {* search input field *}
        <input type="text"
               id="{$facet->getFieldName()|escape:'htmlall'}"
               name="{$facet->getFieldName()|escape:'htmlall'}"
               value="{$facet->getValue()}"
               placeholder="{$facet->getLabel()}"
               onkeyup="if(event.which == 13 || document.getElementById('{$facet->getFieldName()|escape:'htmlall'}').value == ''){ldelim}if (document.getElementById('{$facet->getFieldName()|escape:'htmlall'}').value != '') document.getElementById('{$facet->getFieldName()|escape:'htmlall'}').blur(); $('.facet--{$facet->getFacetName()|escape:'htmlall'}').trigger('onChange');return false;{rdelim}"
               style="margin-left: -1px; margin-top: -1px; height: calc(100% - 2px); border: 0; width: calc(100% + 2px);"
        />

    </div>

</div>
