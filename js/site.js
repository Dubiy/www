jQuery(document).ready(function() {
  jQuery('.questions .question .rating .plus, .questions .question .rating .minus').unbind('click').click(function() {
    var question_id = jQuery(this).closest('.question').attr('question_id');
    var action = (jQuery(this).hasClass('plus')) ? (1) : (-1);
    post_vote('questions', question_id, action, this);
  });

  jQuery('.answers .answer .rating .plus, .answers .answer .rating .minus').unbind('click').click(function() {
    var answer_id = jQuery(this).closest('.answer').attr('answer_id');
    var action = (jQuery(this).hasClass('plus')) ? (1) : (-1);
    post_vote('answers', answer_id, action, this);
  });

  jQuery('.age_selector .submit').unbind('click').click(function() {

    var age_selector = jQuery(this).closest('.age_selector');
    var filter = age_selector.attr('filter');
    if (! filter) {
      filter = 'all';
    }
    var start = parseInt(age_selector.find('input.age_start').val());
    var stop = parseInt(age_selector.find('input.age_stop').val());
    if (start) {
      if (stop) {
        document.location = '/' + filter + '/age/' + start + '/' + stop;
      } else {
        document.location = '/' + filter + '/age/' + start;
      }
    }

    return false;
  });

});

function post_vote(entity, entity_id, action, self) {
  jQuery.post('/ajax/vote', {'entity': entity, 'entity_id': entity_id, 'action': action}, function(data) {
      if (data == 'false') {
        var value = jQuery(self).closest('.rating').find('.value');
        value.css('color', 'red');
        setTimeout(function() {
          value.removeAttr('style');
        }, 800);
      } else {
        jQuery(self).closest('.rating').find('.value').html(data);
      }
    });
}