(function($){
    var FORMHYBRID = {

        init : function(){
            this.initSubPalettes();
        },
        initSubPalettes: function() {
            $('.submitOnChange input[type="checkbox"], .submitOnChange input[type="radio"]').removeAttr('onclick').on('change', function(event) {
                var $this = $(this),
                    $form = $this.closest('form');

                // set hidden field skipvalidation
                $form.find('[name="skipvalidation"]').val('1');
                $form.find('[type="submit"]').trigger('click');
            });
        }
    }

    $(document).ready(function(){
        FORMHYBRID.init();
    });

})(jQuery);