document.addEventListener('DOMContentLoaded', function () {
    var enableCheckbox = document.querySelector('input[data-toggle-read-more]');
    var textInput = document.querySelector('input[name="pgs_read_more_text"]');

    if (enableCheckbox && textInput) {
        // Initialize display based on current checkbox state
        textInput.style.display = enableCheckbox.checked ? 'block' : 'none';

        enableCheckbox.addEventListener('change', function () {
            textInput.style.display = this.checked ? 'block' : 'none';
        });
    }
});
