CraftPermissionCheckboxToggler = {
    $form: $('.content form').first(),
    init: function() {
        var self = this;
        var pageType = arguments[0] || 'groups';

        console.log(pageType);
        

        // Either this is the groups or users page
        if (pageType === 'groups') {
            this.$form = $('.content form').first();

            var $form = this.$form,
                $mainHeading = $form.find('h2').first(),
                $headings = $form.find('h3'),
                $lists = $form.find('h3 + ul.indent');
        } else {
            this.$form = $('#permissions').first();
            var $form = this.$form,
                $mainHeading = $form.parent().find('h2').filter(':contains(Permissions)'),
                $headings = $form.find('h3'),
                $lists = $form.find('h3 + ul.indent');
        }

        var $link = $('<a />').attr('class', 'js-permissiontoggler-checkCheckboxes').text('Check all').attr('data-action', 'checkall');
        var $unlink = $('<a />').attr('class', 'js-permissiontoggler-checkCheckboxes js-permissiontoggler-uncheckCheckboxes').text('Uncheck all').attr('data-action', 'uncheckall');;

        $mainHeading.append($link);
        $mainHeading.append($unlink);

        $headings.each(function(index, element) {
            var $heading = $(this);

            // Links per section
            var $link = $('<a />').attr('class', 'js-permissiontoggler-checkCheckboxes').text('Check').attr('data-set', 'checkboxset-' + index).attr('data-action', 'check');
            var $unlink = $('<a />').attr('class', 'js-permissiontoggler-checkCheckboxes js-permissiontoggler-uncheckCheckboxes').text('Uncheck').attr('data-set', 'checkboxset-' + index).attr('data-action', 'uncheck');;

            $heading.append($link);
            $heading.append($unlink);
            $heading.next('.indent').attr('data-set', 'checkboxset-' + index);
        });

        $(document).on('click', '.js-permissiontoggler-checkCheckboxes', function(e) {
            e.preventDefault();
            var $link = $(e.currentTarget),
                action = $link.data('action');

                console.log(action, CraftPermissionCheckboxToggler.$form);
                

            if ( action === 'checkall' || action === 'uncheckall' ) {
                $list = CraftPermissionCheckboxToggler.$form.find('ul');
                action = action === 'checkall' ? 'check' : 'uncheck';
            } else {
                $list = CraftPermissionCheckboxToggler.$form.find('ul[data-set='+ $link.attr('data-set') +']');
            }

            CraftPermissionCheckboxToggler.toggleCheckboxList( action, $list );
        });

        //CraftPermissionCheckboxToggler.bindEvents();
    },

    toggleCheckboxList: function () {
        var action = arguments[0];
            $list = arguments[1];

        var $checkboxes = $list.find(':checkbox');
        $checkboxes.each(function() {
            var $box = $(this);

            if ( action === 'check' ) {
                if ( ! $box.prop('checked') ) {
                    $box.click();
                }
            } else if ( action === 'uncheck' ) {
                if ( $box.prop('checked') ) {
                    $box.click();
                }
            }
        });
    }
}