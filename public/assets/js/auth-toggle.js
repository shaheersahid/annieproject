$(document).ready(function() {
    // Toggle password visibility
    $('.toggle-password').on('click', function() {
        const button = $(this);
        const input = button.closest('.input-group').find('input');
        const openSvg = button.find('.eye-open');
        const closedSvg = button.find('.eye-closed');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            openSvg.css('display', 'none');
            closedSvg.css('display', 'block');
        } else {
            input.attr('type', 'password');
            openSvg.css('display', 'block');
            closedSvg.css('display', 'none');
        }
        
        // Retain input focus for perfect user interaction
        input.focus();
    });
});
