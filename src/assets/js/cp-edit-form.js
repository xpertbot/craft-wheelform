jQuery(document).ready(function($){
    var $form = $('#main-form');

    if($form.length > 0){
        var index;
        var dirtyFields = $('#changed_fields');
        setIndex();

        $form.find('input').focus(function(){
            if(dirtyFields.val() == '0'){
                dirtyFields.val('1');
            }
        });

        $form.find('.form-field-add').click(function(ev){
            ev.preventDefault();

            $parent = $(this).parents('tr');
            setIndex();
            addRow($parent);
        });

        $form.find('.form-field-rm').click(function(ev){
            ev.preventDefault();

            if(dirtyFields.val() == '0'){
                dirtyFields.val('1');
            }

            $(this).parents('tr').remove();
        });

        function setIndex(){
            index = ($form.find('tbody tr').length + 1);
        }

        function addRow(target){
            var $newRowHtml = $('<tr/>');
            var $textContainer = $('<td/>')
                .append($('<div />', {
                    'class': "field",
                    'html': $('<div/>', {
                        'class': 'input ltr',
                        'html': $('<input/>', {
                            'type': 'text',
                            'class': 'text fullwidth',
                            'name': 'fields['+ index +'][name]'
                        })
                    })
                }));
            var $selectContainer = $('<td/>', {
                'html': $('<div />', {
                    'class': "field",
                    'html': $('<div/>', {
                        'class': 'input ltr',
                        'html': $('<div />', {
                            'class': 'select',
                            'html': $('<select/>', {
                                'name': 'fields['+ index +'][type]',
                                'html': $('<option/>').val('text').text('Text')
                                    .add($('<option/>').val('email').text('Email'))
                                    .add($('<option/>').val('number').text('Number'))
                                    .add($('<option/>').val('checkbox').text('Checkbox'))
                                    .add($('<option/>').val('radio').text('Radio'))
                                    .add($('<option/>').val('hidden').text('Hidden'))
                                    .add($('<option/>').val('select').text('Select'))
                                    .add($('<option/>').val('file').text('File'))
                            })
                        })
                    })
                })
            });

            var $checkboxContainer = $('<td/>', {
                'html': $('<div/>', {
                    'class': "clearfix",
                    'html':
                        $('<div/>', {
                            'class': 'field checkboxfield',
                            'html':  $('<input/>', {
                                'type': 'hidden',
                                'name': 'fields['+ index +'][required]'
                            })
                            .add($('<input/>', {
                                    'type': 'checkbox',
                                    'class': 'checkbox',
                                    'name': 'fields['+ index +'][required]',
                                    'value': '1',
                                    'id':'requiredcheckbox'+index
                                })
                                .val('1'))
                            .add($('<label/>', {
                                    'for': 'requiredcheckbox'+index
                                }).text('Required'))
                        })
                }).add($('<div/>', {
                    'class': "clearfix",
                    'html':
                        $('<div/>', {
                            'class': 'field checkboxfield',
                            'html':  $('<input/>', {
                                'type': 'hidden',
                                'name': 'fields['+ index +'][index_view]'
                            })
                            .add($('<input/>', {
                                    'type': 'checkbox',
                                    'class': 'checkbox',
                                    'name': 'fields['+ index +'][index_view]',
                                    'value': '1',
                                    'id':'index_viewcheckbox'+index
                                })
                                .val('1'))
                            .add($('<label/>', {
                                    'for': 'index_viewcheckbox'+index
                                }).text('Entries View'))
                        })
                }))
            });

            var $settingsTd = $('<td/>', {
                'html': $('<a/>', {
                    'class': 'form-field-rm right',
                    'href': '#',
                    'data-icon': 'remove'
                }).on('click', function(ev){
                    ev.preventDefault();
                    $(this).parents('tr').remove();
                })
                .add($('<a/>', {
                        'class': 'form-field-add',
                        'href': '#',
                        'data-icon': 'plus'
                    }).on('click', function(ev){
                        ev.preventDefault();

                        $parent = $(this).parents('tr');
                        setIndex();
                        addRow($parent);
                    })
                )
            })
            $newRowHtml.append($textContainer);
            $newRowHtml.append($selectContainer);
            $newRowHtml.append($checkboxContainer);
            $newRowHtml.append($settingsTd);

            target.after($newRowHtml);
        }
    }
});
