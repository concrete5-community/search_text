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
            var html = '<div class="result">';

            html += '<header>' + result.relative_path_highlighted + '</header>';

            if (result.content_highlighted) {
                html += '<div>' + result.content_highlighted + '</div>';
            }

            html += '</div>';

            return html;
        }

        $.post(CCM_DISPATCHER_FILENAME + '/ccm/search_text/search_files', formData)
            .done(function(e){
                 if (e.results && e.results.length) {
                     $progressElement.html(SEARCH_TEXT_SEARCH_FINISHED + ' ' + e.results.length);

                     e.results.forEach(function(result) {
                        $resultsElement.prepend(transformResult(result));
                     });
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
