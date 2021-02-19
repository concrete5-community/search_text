$(document).ready(function() {
    var $form = $('#search-text-form');
    var $searchFor = $form.find('[name="searchFor"]');
    var $submitButton = $form.find('[type="submit"]');
    var $resultsContainer = $('#search-text-results-container');
    var $progressElement = $('#search-text-progress');
    var $resultsElement = $('#search-text-results');

    $form.on('submit', function(e) {
        e.preventDefault();

        if ($submitButton.hasClass('disabled')) {
            return false;
        }

        $resultsContainer.removeClass('hide');
        $progressElement.html(SEARCH_TEXT_SENT_PENDING);
        $resultsElement.html('');

        $submitButton.addClass('disabled');

        search($(this).serialize());

        return false;
    });

    function search(formData) {
        var lastResponseLength = false;
        var searchForValue = $searchFor.val();

        function transformResult(result) {
            var identifiers = [];
            result.identifiers.forEach(function(identifier){
                identifiers.push(identifier.key + ': ' + identifier.value);
            });

            var html = '<div class="result">';

            var url = encodeURI(
                CCM_DISPATCHER_FILENAME
                + '/ccm/search_text/view_record?table='
                + result.table
                + '&identifiers='
                + JSON.stringify(result.identifiers)
                + '&search_for='
                + searchForValue
            );

            html += '<a href="' + url + '" class="dialog-launch" dialog-width="800" dialog-height="600" dialog-modal="true">';

            html += '<header>' + result.table + ' (' + identifiers.join(', ') + ')</header>';
            html += '<div>' + result.search_result + '</div>';

            html += '</a>';

            html += '</div>';

            return html;
        }

        $.post(CCM_DISPATCHER_FILENAME + '/ccm/search_text/search', formData)
            .done(function(e){
                 if (e.results && e.results.length) {
                     $progressElement.html(SEARCH_TEXT_SEARCH_FINISHED + ' ' + e.results.length);

                     e.results.forEach(function(result) {
                        $resultsElement.prepend(transformResult(result));
                     });

                     $('a.dialog-launch').dialog();
                 } else {
                     $progressElement.html(SEARCH_TEXT_NO_RESULTS);
                 }
            })
            .error(function(e) {
                $progressElement.html(SEARCH_TEXT_ERROR_OCCURRED);
                var response = JSON.parse(e.responseText);
                var error = response.error.message ? response.error.message : response.error;

                $resultsElement.prepend(
                    '<div class="alert alert-danger">' + error + '</div>'
                );
            })
            .always(function() {
                $submitButton.removeClass('disabled');
            });
    }
});
