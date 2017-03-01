(function ($) {
    $(document).ready(function(){
        var API_URL = 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed?';
        var CHART_API_URL = 'http://chart.apis.google.com/chart?';

        // Object that will hold the callbacks that process results from the
        // PageSpeed Insights API.
        var callbacks = {
            render_results: render_results
        };

        // Invokes the PageSpeed Insights API. The response will contain
        // JavaScript that invokes our callback with the PageSpeed results.
        function runPagespeed(strategy) {
            var s = document.createElement('script');
            s.type = 'text/javascript';
            s.async = true;
            var query = [
                'url=' + window.URL_TO_GET_RESULTS_FOR,
                'callback=runPagespeedCallback_'+strategy,
                'strategy='+strategy,
                'screenshot=true',
                'key=' + window.GOOGLE_API_KEY,
            ].join('&');
            s.src = API_URL + query;
            document.head.insertBefore(s, null);
        }

        window.runPagespeedCallback_mobile = function(results){
            results.strategy = 'mobile';
            runPagespeedCallbacks(results);
        }

        window.runPagespeedCallback_desktop = function(results){
            results.strategy = 'desktop';
            runPagespeedCallbacks(results);
        }

        // Our JSONP callback. Checks for errors, then invokes our callback handlers.
        window.runPagespeedCallbacks = function (result) {
            if (result.error) {
                var errors = result.error.errors;
                for (var i = 0, len = errors.length; i < len; ++i) {
                    alert(errors[i].message);
                }
                return;
            }

            // Dispatch to each function on the callbacks object.
            for (var fn in callbacks) {
                var f = callbacks[fn];
                if (typeof f == 'function') {
                    callbacks[fn](result);
                }
            }
        }

        // Invoke the callback that fetches results.
        setTimeout(function(){
            runPagespeed('desktop');
            runPagespeed('mobile');
        }, 0);

        function format_text(object){
            var stext = object.format;
            if(object.args){
                for(var j = 0; j < object.args.length; j++){
                    if(object.args[j].key == 'LINK' ){
                        stext = stext.replace('{{BEGIN_LINK}}', '<a href="'+object.args[j].value+'" target="_BLANK">');
                        stext = stext.replace('{{END_LINK}}', '</a>');
                    }else if(object.args[j].type == 'STRING_LITERAL'){
                        var value = object.args[j].value.replace(/</g,"&lt;").replace(/>/g,"&gt;");
                        stext = stext.replace('{{' + object.args[j].key + '}}', '<strong>'+value+'</strong>');
                    }else{
                        stext = stext.replace('{{' + object.args[j].key + '}}', '<strong>'+object.args[j].value+'</strong>');
                    }
                }
            }

            return stext;
        }

        function render_results( result ){
            //console.log(result);
            var tab_template = $('<div class="tab"><input type="radio" id="tab-' + result.strategy + '" name="tab-group-1"><label for="tab-' + result.strategy + '" style="left: '+(pagespeed_results_tab_position)+'px">' + result.strategy + '</label><div class="content"></div></div>');
            pagespeed_results_tab_position += 75;
            var results = [];
            var ruleResults = result.formattedResults.ruleResults;
            for (var i in ruleResults) {
                var ruleResult = ruleResults[i];
                results.push({name: ruleResult.localizedRuleName,
                    impact: ruleResult.ruleImpact,
                    summary: typeof ruleResult.summary != 'undefined' ? ruleResult.summary : null,
                    urlBlocks: typeof ruleResult.urlBlocks != 'undefined' ? ruleResult.urlBlocks : null });
            }
            results.sort(sortByImpact);
            var ul = document.createElement('ul');
            for (var i = 0, len = results.length; i < len; ++i) {
                var r = document.createElement('li');
                var impactclass = "green";
                if( results[i].impact > 0 && results[i].impact <= 50 ) impactclass = "yellow";
                if( results[i].impact > 50 ) impactclass = "red";
                r.className += impactclass;
                r.innerHTML = '<strong>'+results[i].name+'</strong>';
                var details = '';
                if(results[i].summary || results[i].urlBlocks) details += '<a href="#" class="readmore"><strong> Details...</strong></a>';
                details +=  '<div class="readmore-content">';
                if(results[i].summary){
                    details +=  '<span>' + format_text(results[i].summary) + '</span>' ;
                }
                if(results[i].urlBlocks){
                    for(var j = 0; j < results[i].urlBlocks.length; j++){
                        if(results[i].urlBlocks[j].header){
                            details += '<br/><span>' + format_text(results[i].urlBlocks[j].header) + '</span>' ;
                        }
                        if(results[i].urlBlocks[j].urls){
                            details += '<ul>';
                            for(var k = 0; k < results[i].urlBlocks[j].urls.length; k++){
                                details += '<li>'+format_text(results[i].urlBlocks[j].urls[k].result)+'</li>';
                            }
                            details += '</ul>';
                        }
                    }
                }
                details += '</div>';
                r.innerHTML += details;
                ul.insertBefore(r, null);
            }
            if (ul.hasChildNodes()) {
                tab_template.find('.content').append(ul);
            } else {
                var div = document.createElement('div');
                div.innerHTML = 'No high impact suggestions. Good job!';
                tab_template.find('.content').append(div);
            }

            $('.pagespeed-results .tabs').append(tab_template);

            tab_template.find("input").trigger("click");

            // Helper function that sorts results in order of impact.
            function sortByImpact(a, b) { return b.impact - a.impact; }

            tab_template.find(".readmore-content").hide();
            tab_template.find(".readmore").click(function(event){
                event.preventDefault();
                $(this).next(".readmore-content").fadeToggle();
            });
            tab_template.find(".content").prepend("<hr/>");
            $("#template-list").clone().prependTo(tab_template.find(".content")).show();

            if(result.ruleGroups){
                var scoresHtml = '<h2>Scores</h2><br/>';
                if(result.ruleGroups.USABILITY){
                    var className = 'green';
                    if(result.ruleGroups.USABILITY.score < 95) className = "yellow";
                    if(result.ruleGroups.USABILITY.score < 70) className = "red";
                    scoresHtml += '<h3 class="'+className+'">Usability: '+result.ruleGroups.USABILITY.score+'/100</h3><br/>'
                }
                if(result.ruleGroups.SPEED){
                    var className = 'green';
                    if(result.ruleGroups.SPEED.score < 95) className = "yellow";
                    if(result.ruleGroups.SPEED.score < 70) className = "red";
                    scoresHtml += '<h3 class="'+className+'">Speed: '+result.ruleGroups.SPEED.score+'/100</h3><br/>'
                }
                scoresHtml += '<hr/>';
                tab_template.find(".content").prepend(scoresHtml);
            }

            var screenshot = document.createElement('img');
            screenshot.src = 'data:'+result.screenshot.mime_type+';charset=utf-8;base64,'+result.screenshot.data.replace(/_/g,"/").replace(/-/g, "+");
            //console.log(screenshot.src);
            $(screenshot).css('float', 'right');
            $(screenshot).css('width', "40%");
            $(screenshot).prependTo(tab_template.find(".content"));
            $(".pagespeed-results .loading-spinner").hide();
        }

        window.pagespeed_results_tab_position = 0;
    });
})(jQuery);

