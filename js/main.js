function ListScroller($container, $list){
    this.$container = $container;
    this.$list = $list;

    this.listOffset = 0;
    this.listWidth = 0;
    this.inactiveMargin = 100;
    this.listScrollableWidth = 0;

    this.updateSizes();
    this.registerListeners();
}

$.extend(ListScroller.prototype, {
   registerListeners: function(){
       this.$container.on('mousemove', this.onMouseMove.bind(this));

       var self = this;
       $(window).on('resize', function(){
          self.updateSizes();
       });
   },

    updateSizes: function(){
        this.listWidth = this.$list.outerWidth(true);
	    this.listScrollWidth = this.$list[0].scrollWidth;
	    this.listWidthDiff = (this.listScrollWidth / this.listWidth) - 1;
        this.listScrollableWidth = this.listWidth - 2*this.inactiveMargin;
	    this.listFidderenceRatio = this.listWidth / this.listScrollableWidth;
    },

    onMouseMove: function(e){
	    var mX = e.pageX - this.$container.offset().left - this.$list.offset().left;
	    var mX2 = Math.min(Math.max(0,mX-this.inactiveMargin), this.listScrollableWidth) * this.listFidderenceRatio;

        this.listOffset += (mX2 - this.listOffset);
		this.$list.scrollLeft(this.listOffset * this.listWidthDiff);
    }
});


$(function(){
	$("#main-navigation ul li a").click(function(e){
		e.preventDefault();

		var href = $(this).attr('href');
		$("html, body").animate({scrollTop: $(href).offset().top}, 'slow');
	});

    var $originals_picker = $('#originals-picker');
    var $originals_picker_container = $originals_picker.find('.picker-container');
    var $originals_picker_list = $originals_picker_container.find('.picker-list');
	var $originals_cover = $("#originals-cover");
	var $originals_cover_container = $originals_cover.find('.originals-cover-container');

    var scroller = new ListScroller($originals_picker_container, $originals_picker_list);

	$originals_picker_list.find('div').click(function(){
		var dataRef = $(this).attr('data-ref');
		var image = $(this).css('background-image');

		console.log(image);
		$originals_cover_container.css({'background-image': image});
	});

	var contactTriggered = false;
	var waypointTriggered = false;
	$('#sections-container > .section').waypoint(function(direction){
		var $active = $(this);

		if (direction === "up") {
			$active = $active.prev();
		}

		if (!$active.length) {
			$active = $(this);
		}

		var dataRef = $active.attr('data-ref');

		$("#main-navigation div ul li").removeClass('active');
		$("#" + dataRef).addClass('active');
		waypointTriggered = true;
		contactTriggered = false;
	}, {offset: 0});


	$(window).scroll(function() {
		if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
			$("#main-navigation div ul li").removeClass('active');
			$("#contact-link").addClass('active');
			contactTriggered = true;
			waypointTriggered = false;
		} else {
			if(contactTriggered && !waypointTriggered){
				$("#main-navigation div ul li").removeClass('active');
				$("#genre-link").addClass('active');
			}
		}
	});
});