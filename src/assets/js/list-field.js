(function($){
    var addAnchor = $('.wf-list-add');
    if(addAnchor) {
        addAnchor.click(function(e){
            e.preventDefault();
            var el = $(this);
            var parent = $(this).parent();
            addNewField(parent, el.data('field-name'));
        });
    }

    function addNewField(container, fieldName)
    {
        var field = $('<input>').attr({"type": "text", "name": fieldName + "[]"});
        container.append(field);
    }
})(jQuery);
