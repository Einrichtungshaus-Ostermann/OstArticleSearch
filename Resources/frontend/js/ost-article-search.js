
/**
 * Einrichtungshaus Ostermann GmbH & Co. KG - Article Search
 *
 * @package   OstArticleSearch
 *
 * @author    Eike Brandt-Warneke <e.brandt-warneke@ostermann.de>
 * @copyright 2018 Einrichtungshaus Ostermann GmbH & Co. KG
 * @license   proprietary
 */

;(function ($) {

    // use strict mode
    "use strict";

    // detail plugin
    $.plugin( "ostArticleSearch", {

        // our configuration
        configuration: {
            searchUrl: null
        },

        // on initialization
        init: function ()
        {
            // get this
            var me = this;

            // set configuration
            me.configuration.searchUrl = ostArticleSearch.searchUrl;

            // double click on hidden button on every site
            me._on( me.$el.find( "button" ), 'click', $.proxy( me.onSearchClick, me ) );
        },

        // ...
        onSearchClick: function ( event )
        {
            // dont click
            event.preventDefault();

            // ...
            var me = this;

            // search string
            var search = "";

            // get every search input
            me.$el.find( 'input[type="text"]' ).each( function() {

                // get the input
                var $input = $( this );

                // default article search?
                if ( $input.attr("name").indexOf("article_") < 0 )
                    // it isnt
                    return;

                // get the value
                var value = $input.val().trim();

                // empty?!
                if ( value == "" )
                    // next
                    return;

                // add it to search
                search = search + value + " ";
            });

            // trim it
            search = search.trim();

            // we need a search
            if ( search == "" )
                // stop
                return;

            // get the url
            var url = me.configuration.searchUrl.replace( "__search__", search );

            // do we have a price
            if ( me.$el.find( 'input[type="text"].price' ).val().trim() != "" )
                // add it to search
                url = url + "&max=" + me.$el.find( 'input[type="text"].price' ).val().trim();

            // load search
            $.ostFoundationLoadingIndicator.open();
            window.location.href = url;
        },

        // on destroy
        destroy: function()
        {
            // get this
            var me = this;

            // call the parent
            me._destroy();
        }


    });


    // call our plugin
    $( "body.is--ctl-ostarticlesearch.is--act-search div.content--ost-article-search form" ).ostArticleSearch();

})(jQuery);
