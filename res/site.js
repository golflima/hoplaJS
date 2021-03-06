/*!
 *   _                 _           _ ____  
 *  | |__   ___  _ __ | | __ _    | / ___| 
 *  | '_ \ / _ \| '_ \| |/ _` |_  | \___ \ 
 *  | | | | (_) | |_) | | (_| | |_| |___) |
 *  |_| |_|\___/| .__/|_|\__,_|\___/|____/ 
 *              |_|                        
 * 
 * This file is part of hoplaJS.
 * See: <https://github.com/golflima/hoplaJS>.
 *
 * Copyright (C) 2017 Jérémy Walther <jeremy.walther@golflima.net>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Otherwise, see: <https://www.gnu.org/licenses/agpl-3.0>.
 */

var minify = require('html-minifier').minify;

var minifyOptions = {
    collapseBooleanAttributes       : true,
    collapseWhitespace              : true,
    conservativeCollapse            : true,
    decodeEntities                  : true,
    html5                           : true,
    minifyCSS                       : true,
    minifyJS                        : true,
    processConditionalComments      : true,
    removeAttributeQuotes           : true,
    removeComments                  : true,
    removeEmptyAttributes           : true,
    removeOptionalTags              : true,
    removeRedundantAttributes       : true,
    removeScriptTypeAttributes      : true,
    removeStyleLinkTypeAttributes   : true,
    trimCustomFragments             : true,
    useShortDoctype                 : true
}

var uglifyJsDefaultOptions = {
    parse: {
        strict          : false
    },
    compress: {
        sequences       : true,
        properties      : true,
        dead_code       : true,
        drop_debugger   : true,
        unsafe          : true,
        unsafe_comps    : true,
        conditionals    : true,
        comparisons     : true,
        evaluate        : true,
        booleans        : true,
        loops           : true,
        unused          : true,
        hoist_funs      : true,
        hoist_vars      : false,
        if_return       : true,
        join_vars       : true,
        cascade         : true,
        side_effects    : true,
        negate_iife     : true,
        screw_ie8       : false,
        warnings        : true,
        global_defs     : {}
    },
    output: {
        indent_start    : 0,
        indent_level    : 4,
        quote_keys      : false,
        space_colon     : true,
        ascii_only      : false,
        inline_script   : true,
        width           : 80,
        max_line_len    : 32000,
        beautify        : false,
        source_map      : null,
        bracketize      : false,
        semicolons      : true,
        comments        : /@license|@preserve|^!/,
        preserve_line   : false,
        screw_ie8       : false
    }
};

function base64_encode(text) {
    return window.btoa(unescape(encodeURIComponent(text))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
}

function uglifyJs(input, options) {
	var parseOptions = defaults(defaults({}, options.parse), uglifyJsDefaultOptions.parse, true);
	var compressOptions = defaults(defaults({}, options.compress), uglifyJsDefaultOptions.compress, true);
	var outputOptions = defaults(defaults({}, options.output), uglifyJsDefaultOptions.output, true);

	var topLevelAst = parse(input, parseOptions);
	topLevelAst.figure_out_scope();

	var compressedAst = topLevelAst.transform(new Compressor(compressOptions));

	compressedAst.figure_out_scope();
	compressedAst.compute_char_frequency();
	compressedAst.mangle_names();

	return compressedAst.print_to_string(outputOptions);
}

$(document).ready(function(){
    // Init 'Generate the HoplaJS URLs for this script !' button ...
    $('.onclick-generate').click(function() {
        var $button = $(this).button('loading');
        var javascript = $('#javascript').val();
        var dependencies = $('#dependencies').val();
        var css = $('#css').val();
        var body = $('#body').val();
        if ($('#minifyJs').is(':checked')) {
            javascript = uglifyJs(javascript, uglifyJsDefaultOptions);
        }
        if ($('#minifyBody').is(':checked')) {
            body = minify(body, minifyOptions);
        }
        $.ajax({
            type: "POST",
            url: baseUrl + '/api/encode',
            data: {
                javascript: javascript,
                dependencies: dependencies,
                css: css,
                body: body,
            },
            success: function(data) {
                $('#urlEdit').val(data.baseUrl + '/edit/' + data.data);
                $('#urlRaw').val(data.baseUrl + '/raw/' + data.data);
                $('#urlRun').val(data.baseUrl + '/run/' + data.data);
                $('.urlHash').html('&mdash; hash: ' + data.hash);
                var urlSize = (data.baseUrl + '/run/' + data.data).length;
                $('#urlSize').html(urlSize);
                $('#urlSize').removeClass('bg-success bg-warning bg-danger');
                if (urlSize <= 2048) {
                    // URL length below 2048 are OK on every browsers
                    // but over 2048 it won't work with MS IE
                    $('#urlSize').addClass('bg-success');
                    $('#urlSizeProgress1').css('width', urlSize * 100 / 2048 + '%');
                    $('#urlSizeProgress2').css('width', '0%');
                    $('#urlSizeProgress3').css('width', '0%');
                } else if (urlSize <= 8000) {
                    // URL length over 8000 won't work on Android, and will be blocked by Apache by default
                    $('#urlSize').addClass('bg-warning');
                    $('#urlSizeProgress1').css('width', 2047 * 100 / 8000 + '%');
                    $('#urlSizeProgress2').css('width', (urlSize - 2048) * 100 / 8000 + '%');
                    $('#urlSizeProgress3').css('width', '0%');
                }
                else {
                    // URL length over 32779 won't work on Google Chrome
                    // It seems Firefox and Safari are able to handle URL length over 65535
                    $('#urlSize').addClass('bg-danger');
                    $('#urlSizeProgress1').css('width', 2047 * 100 / 32779 + '%');
                    $('#urlSizeProgress2').css('width', (8000 - 2048) * 100 / 32779 + '%');
                    $('#urlSizeProgress3').css('width', (urlSize - 2048 - 8000) * 100 / 32779 + '%');
                }
                $button.button('reset');
            },
            dataType: 'json'
        });
    });

    // Init 'Copy' buttons ...
    $('.onclick-copy').click(function() {
        var toCopy = $(this).parent().parent().find('input:last').attr('id');
        document.getElementById(toCopy).select();
        var copied;
        try {
            copied = document.execCommand('copy');
        } catch (ex) {
            copied = false;  
        }
        if (!copied) {
            alert('Sorry, your browser doesn\'t support copying to clipboard.');
        }
    });

    // Init 'Test' buttons ...
    $('.onclick-test').click(function() {
        var url = $(this).parent().parent().find('input:last').val();
        var opened = window.open(url, '_blank');
        if (opened) {
            opened.focus();
        } else {
            alert('Please allow popups for this website.');
        }
    });

    // Init ToolBox - Proxy
    $('#proxyUrlRaw').change(function() {
        $('#proxyUrl').val(baseUrl + '/api/proxy/' + base64_encode($('#proxyUrlRaw').val()) + 
            ($('#proxyUrlContentType').val() != '' ? '/' + base64_encode($('#proxyUrlContentType').val()) : ''));
    });
    $('#proxyUrlContentType').change(function() {
        $('#proxyUrlRaw').change();
    });

    // Init tooltips
    $('[data-toggle="tooltip"]').tooltip({
        html: true,
        trigger: 'click hover focus'
    });
});