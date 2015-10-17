// Table of Contents
var toc = {
  $obj: $("article .meta .index"),
  position: {},
  initialHeight: 0,
  $headings: $("h2"),
  all: [],
  threshold: 0,
  stop: 0,

  init: function(skipEvents = false) {
    if (this.$obj.length > 0) {
      $.each(this.$headings, function(k,v) {
        $(v).data("top", parseInt($(v).offset()["top"]));
      });
      
      this.position = this.$obj.parent().offset();
      this.position["top"] = this.position["top"] + 25;
      this.position["left"] = this.position["left"] + 26;
      
      this.initialHeight = toc.$obj.outerWidth();
      
      this.threshold = parseInt($("article").offset()["top"] - $(".main_navigation").outerHeight());
              
      this.stop = parseInt((this.$obj.parent().offset()["top"] + this.$obj.parent().outerHeight()) - $(".main_navigation").outerHeight() - this.$obj.outerHeight() - 60 - 15);

      toc.fix();

      if (!skipEvents)
        this.initEvents();
    }
  },

  initEvents: function() {
    $(window).on("scroll", function() {
      toc.scrollSpy();
      toc.fix();
    });

    $(window).on("resize", function() {
      toc.init(true);
    });
  },

  scrollSpy: function() {
    var scrollTop = $(window).scrollTop() - 1;

    toc.all = [];

    $.each(toc.$headings, function(h) {
      var $this = $(this),
          top = $this.data("top");

      var onScreen = (top >= scrollTop && top <= $(window).innerHeight() + scrollTop);

      if (onScreen)
        toc.all.push($this.attr("id"));
    });

    if (toc.all.length > 0) {
      var links = $(".index > ol li a");

      $.each(links, function() {
        var $this = $(this),
            $li = $this.parent("li");

        $li.prop("class", "");

        if ($.inArray($this.attr("href").substring(1), toc.all) > -1)
          $li.addClass("active");
      });
    }
  },

  fix: function() {
    if ($(document).scrollTop() <= this.threshold) // Top
      this.$obj
      .removeClass("fixed bottom")
      .prop("style", "");
    else if ($(document).scrollTop() >= this.stop) // Bottom
      this.$obj
      .removeClass("fixed")
      .addClass("bottom")
      .prop("style", "");
    else // Fixed
      this.$obj
      .removeClass("bottom")
      .addClass("fixed")
      .css("top", parseInt(toc.position["top"] - toc.threshold))
      .css("right", parseInt($(window).outerWidth() - toc.position["left"] - toc.initialHeight));
  }
}; toc.init();