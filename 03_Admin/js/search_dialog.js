    $(".jquery-ui-opener-p").live("click",function(){
      var idx = $('.jquery-ui-opener-p').index(this);
      player_search(idx);
      $("#jquery-ui-dialog-p").dialog("open");
     return false;
    });
    $('.player_select').live('click',function(){
      var idx = $('.player_select').index(this); 
      var name = $('span.player_name').eq(idx).html();
      var id = $('span.player_id').eq(idx).html();
      $('.player_tagme').tagify('add',name);
      $("#jquery-ui-dialog-p").dialog("close");
    });

    $(".jquery-ui-opener-c").live("click",function(){
      var idx = $('.jquery-ui-opener-c').index(this);
      golfclub_search(idx);
      $("#jquery-ui-dialog-c").dialog("open");
     return false;
    });
    $('.course_select').live('click',function(){
      var idx = $('.course_select').index(this); 
      var name = $('span.golfclub_name').eq(idx).html();
      var id = $('span.golfclub_id').eq(idx).html();
      $('.golfclub_tagme').tagify('add',name);
      $("#jquery-ui-dialog-c").dialog("close");
    });

    $(".jquery-ui-opener-t").live("click",function(){
      var idx = $('.jquery-ui-opener-t').index(this);
      tour_search2(idx);
      $("#jquery-ui-dialog-t").dialog("open");
     return false;
    });
    $(".jquery-ui-opener-r").live("click",function(){
      round_search(tour_id);
      $("#jquery-ui-dialog-r").dialog("open");
     return false;
    });
