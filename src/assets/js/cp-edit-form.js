jQuery(document).ready(function($){
    var $form = $('#main-form');

    if($form.length > 0){
        var index;
        setIndex();

        $form.find('.form-field-add').click(function(ev){
            ev.preventDefault();

            $parent = $(this).parents('tr');
            setIndex();
            addRow($parent);
        });

        $form.find('.form-field-rm').click(function(ev){
            ev.preventDefault();
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
                                    .add($('<option/>').val('dropdown').text('Dropdown'))
                            })
                        })
                    })
                })
            });

            var $checkboxContainer = $('<td/>', {
                'html': $('<div/>', {
                    'class': "field checkboxfield",
                    'html': $('<input/>', {
                            'type': 'hidden',
                            'name': 'fields['+ index +'][required]'
                        })
                        .add($('<input/>', {
                                'type': 'checkbox',
                                'class': 'checkbox',
                                'name': 'fields['+ index +'][required]',
                                'id':'checkbox'+index
                            })
                            .val('1'))
                        .add($('<label/>', {
                                'for': 'required'+index
                            }).text('Required'))
                })
            });

            var $settingsTd = $('<td/>', {
                'html': $('<a/>', {
                    'class': 'form-field-rm error right',
                    'href': '#',
                    'data-icon': 'remove'
                }).on('click', function(ev){
                    ev.preventDefault();
                    $(this).parents('tr').remove();
                })
                .add($('<a/>', {
                        'class': 'form-field-add success',
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
