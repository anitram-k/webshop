import './bootstrap.js';
import jQuery from 'jquery';
// Popper.js importálása
import 'popper.js';

// Bootstrap 4 importálása
import 'bootstrap';

// Bootstrap CSS importálása
import 'bootstrap/dist/css/bootstrap.css';

// Bootstrap Icons CSS importálása
import 'bootstrap-icons/font/bootstrap-icons.css';

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

jQuery(function() {

    jQuery(document).on("click", ".ajaxButton", function(e) {
        jQuery.ajax({
            url: jQuery(this).data('url'),
            data: {'action' : jQuery(this).data('action')},
            method: "GET",
            success: function(response) {

                let target = jQuery(e.target).closest('tr').find('.quantity');

                if(target){
                    let count;
                    switch(response.action){
                        case 'add':
                            count = parseInt(target.text()) + 1;
                            console.log('Success:', 'Sikeres kosárhoz adás');
                            break;
                        case 'remove':
                            count = parseInt(target.text()) - 1;
                            console.log('Success:', 'Sikeres törlés a kosárból');
                            break;
                    }

                    if(response.sum){
                        setSumData(response);
                    }

                    document.getElementById('cartBlock').innerHTML = response.cartHtml;
                }
                
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error:', textStatus, errorThrown);
            }
        });
    });


    jQuery(document).on("click", ".deleteButton", function() {

        jQuery.ajax({
            url: jQuery(this).data('url'),
            method: "GET",
            success: function(response) {
                console.log('Cart deleted');
                
                document.getElementById('cartBlock').innerHTML = response.cartHtml;

                if(response.sum){
                    setSumData(response);
                }

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error:', textStatus, errorThrown);
            }
        });
    });

    jQuery(document).on("click", ".saveButton", function(e) {
        jQuery.ajax({
            url: jQuery(this).data('url'),
            method: "GET",
            success: function(response) {
                jQuery('#ajaxMessage').text('A mentés sikeres volt!').fadeIn().delay(3000).fadeOut();
                console.log(response);
                
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error:', textStatus, errorThrown);
            }
        });
    });

    jQuery(document).on("click", ".loadButton", function(e) {
        jQuery.ajax({
            url: jQuery(this).data('url'),
            method: "GET",
            success: function(response) {                
                document.getElementById('cartBlock').innerHTML = response.cartHtml;

                if(response.sum){
                    setSumData(response);
                }
                
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error:', textStatus, errorThrown);
            }
        });
    });


    function setSumData(response) {
        jQuery('.original').text(response.sum.originalSum);
        jQuery('.discount').text(response.sum.discountSum);
        jQuery('.discounted').text(response.sum.discountedSum);
      }
});