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

function base64_encode(text) {
    return window.btoa(unescape(encodeURIComponent(text))).replace('+', '-').replace('/', '_').replace('=', '');
}

$(document).ready(function(){
    // Init 'Generate the HoplaJS URLs for this script !' button ...
    $('.onclick-generate').click(function() {
        var javascript = $('#javascript').val();
        var dependencies = $('#dependencies').val();
        var htmlBody = $('#htmlBody').val();
        $.ajax({
            type: "POST",
            url: baseUrl + '/api/encode',
            data: {
                javascript: javascript,
                dependencies: dependencies,
                htmlBody: htmlBody,
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
                } else if (urlSize <= 8192) {
                    // URL length over 8192 won't work on Android
                    $('#urlSize').addClass('bg-warning');
                    $('#urlSizeProgress1').css('width', 2047 * 100 / 8192 + '%');
                    $('#urlSizeProgress2').css('width', (urlSize - 2048) * 100 / 8192 + '%');
                    $('#urlSizeProgress3').css('width', '0%');
                }
                else {
                    // URL length over 32779 won't work on Google Chrome
                    // It seems Firefox and Safari are able to handle URL length over 65535
                    $('#urlSize').addClass('bg-danger');
                    $('#urlSizeProgress1').css('width', 2047 * 100 / 32779 + '%');
                    $('#urlSizeProgress2').css('width', (8192 - 2048) * 100 / 32779 + '%');
                    $('#urlSizeProgress3').css('width', (urlSize - 2048 - 8192) * 100 / 32779 + '%');
                }
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
        if (copied) {
            $(this)
                .removeClass('btn-primary').addClass('btn-success')
                .delay(1000)
                .removeClass('btn-success').addClass('btn-primary');
        } else {
            $(this)
                .removeClass('btn-primary').addClass('btn-danger')
                .delay(1000)
                .removeClass('btn-danger').addClass('btn-primary');
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
});