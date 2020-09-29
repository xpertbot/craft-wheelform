export default class EntriesService {
  constructor()
  {
    this.init();
  }

  init()
  {
    const jQuery = window.jQuery;
    jQuery(document).ready(($) => {
      let table = $('#wheelform-entries-table');
      let checkboxSelect = table.find('th.checkbox-cell .checkbox');
      checkboxSelect.click(function(ev) {
        let checkbox = $(this);
        checkbox.toggleClass('checked');

        if (checkbox.hasClass('checked')) {
          table.find('td .checkboxfield input.checkbox').prop('checked', 'checked');
        } else {
          table.find('td .checkboxfield input.checkbox').prop('checked', '');
        }
      });
    });
  }
}