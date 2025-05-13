
window.addEventListener('DOMContentLoaded', function () {
    var input = document.querySelector('input[name="localisation"]');
    if (input && window.google && google.maps && google.maps.places) {
        var autocomplete = new google.maps.places.Autocomplete(input, {
            componentRestrictions: { country: 'fr' } 
        });
    }
});
