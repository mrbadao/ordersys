(function() {
  (function($) {
    return $.widget('aoiPMP.repeatFields', {
      options: {
        fields: '.list-group:first',
//        sort_items: ':children',
//        sort_updated: null,
        field_added: null,
        field_removed: null
      },
      _create: function() {
        var _this = this;
/*        this._fields().sortable({
          axis: 'y',
          update: function() {
            _this._update_sort_order();
            return _this._trigger('sort_updated');
          }
        });*/
        this._fields().on('click', '.btn-fields-remove', function(e) {
          var field;
          e.preventDefault();
          field = $(e.target).closest(".fields");
          _this.remove_field(field);
          return _this._trigger('field_removed', null, field);
        });
        return this.element.find('.btn-fields-add:last').on('click', function(e) {
          var field;
          field = _this.add_field();
          return _this._trigger('field_added', null, field);
        });
      },
      _fields: function() {
        return this.element.find(this.options.fields);
      },
      add_field: function() {
        var association, new_field, new_id, regexp, template;
        association = this.element.data('association');
        template = this.element.data('template');
        new_id = new Date().getTime();
        regexp = new RegExp("new_" + association, "g");
        new_field = $(template.replace(regexp, new_id)).appendTo(this._fields());
        this._setup_child_repeat_fields(new_field);
        //this._update_sort_order();
        return new_field;
      },
      remove_field: function(field) {
        field.find('.field-destroy').val("1");
        return field.slideUp();
      },
      
      _setup_child_repeat_fields: function(new_field) {
        return new_field.find('.repeat-field').repeatFields(this.options);
      }
    });
  })(jQuery);

}).call(this);