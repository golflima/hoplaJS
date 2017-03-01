```js
$(document).ready(function() {
    var yql = "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20rss%20where%20url%3D'http%3A%2F%2Fdites.bonjourmadame.fr%2Frss'&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";
    $.getJSON(yql, function(res) {
        var tempDom = $('<rss_temp_dom>').append($.parseHTML(res.query.results.item[Math.floor(Math.random() * res.query.results.item.length)].description));
        var imgSrc = $('img', tempDom).attr('src');
        console.log('imgSrc', imgSrc)
        $.getScript( 'assets/js/vibrant-1.0.min.js', function( data, textStatus, jqxhr ) {
          var img = document.createElement('img');
          img.setAttribute('crossOrigin', '');
          img.setAttribute('src', imgSrc);
          img.addEventListener('load', function() {
            var vibrant = new Vibrant(img);
            var swatches = vibrant.swatches()
            var hex;
            for (var swatch in swatches) {
              if (swatches.hasOwnProperty(swatch) && swatches[swatch]) {
                hex = swatches[swatch].getHex();
                break;
              }
            }
            console.log('swatches.first.getHex()', hex)
            for (var swatch in swatches)
              if (swatches.hasOwnProperty(swatch) && swatches[swatch])
                console.log(swatch, swatches[swatch].getHex())
            $('html').css('background', hex + ' url(' + imgSrc + ') no-repeat center center fixed');
            $('html').css('-webkit-background-size', 'contain');
            $('html').css('-moz-background-size', 'contain');
            $('html').css('-o-background-size', 'contain');
            $('html').css('background-size', 'contain');
          });
        });
    }, "jsonp");
});
```

Url : `http://dev.golflima.net/gl-run-js/?jq=eNqVU-9P2zAQ_Vcqqy02pC4_vq3LJqQxoWmMSUz7ghBykkvj1rGD79JSEP_7Lu0KbCAk5CQXO-d3795zJn1ZhLytwZPSEUyxkmXrc7LBS3W_MLFHqaiIGvwwHt-0EFd6ZaoQTGNR56EeLw7GTZs5m49XN-7zTYrgIKfB4f4u32UMNYeIyM9lBRE4ttENjr7sdKCDo-PB4Ve-CkuAOgt-FtpYm8LUoMvIH3jrzrAMvEbpDIMfgl-kSIGRHvca4pE5BghxygvGuc18aamawwrFpK-nQN8uzn9ISh77o02DmPal-MiFrgnq5roI9SehtGka8IXs68ZEhNNfZ98l6Y0AEbB1hJo515dnhipduhCiXL9G4xlBqt3XsrUDP6VKXekCMI-26XgolUBHwdZTkSBXJopSYMyFmuTBY3CgXZiuEy54NQG16edijSCFQQTC8QzHC5txfRod6H1dW69nKJ7axSQmfuvp1nSds-kEJw662YaEmpBmxGPmYbOWQIo8BsTzaKfWi0S8koB_eZE2RXGyYKjvFgk8cCcumOIZjQ2DkHpY9n5v-LITSUyDxqWhvAKUKvETNl2u7elZ34vq3pYy6srg-dL_jKGBSCuJajiMl3il7n3axU6VU7iVapJxW_OHh38F3OLr0kakx2TBsvxf7s1qz0FZ1eeF2RrJ_0vt-AjliFJkJp9PY2g9a-D3RI9PvxR7sCdUz4dRhIbl7-UsGMRtKO0tFOIF0mgJ2dzS6AlxhPYO2BDmQ4atebmlDnfvyg_vyX4j9aEbiej-2KabTf4Ad72DSw`