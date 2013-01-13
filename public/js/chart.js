var Chart = function(i)
{
    // If we're not running jQuery, ABORT.
    if ($ == undefined) return;

    // Get the chart data
    this.chartData = charts[i];

    // Find certain key elements
    this.$chart = $('#'+this.chartData.id);
    this.$containerControls = this.$chart.find('.chart-controls-container');
    this.$containerScroll   = this.$chart.find('.chart-scroll-container');

    this.$info = this.$chart.find('.chart-info');

    // Dimensions for the event information box
    this.infoWidth = 350;
    this.infoPadding = 20;

    // Sort the events by their ID
    this.events = [];
    for (var i = 0; i < this.chartData.events.length; i++)
    {
        this.events[this.chartData.events[i].id_event] = this.chartData.events[i];
    }

    // Sort the groups by their ID
    this.groups = [];
    for (var i = 0; i < this.chartData.groups.length; i++)
    {
        this.groups[this.chartData.groups[i].id_group] = this.chartData.groups[i];
    }

    // Create the JavaScript scrolling controls
    this.createControls();

    // Add a variable for scrolling
    this.scrollSpeed = 0;

    // Add binds (window resize, click, etc.)
    this.addBinds();

    // Update/initialize the chart's size
    this.updateSizes();

    // Show the information area (hidden before JavaScript comes in)
    this.$info.show();

    // Cached result for the localStorage test below
    this.localStorageSupport = null;

    // If we have a saved scrolling position, scroll to that
    if (this.checkLocalStorageSupport)
    {
        this.$containerScroll.scrollLeft(parseInt(localStorage.getItem('scrollState')));
    }

    // Preload the flag images
    var flagPreload = document.createElement('img');
    flagPreload.src = imgUrl+'/flags.png';
};

/**
 * Checks for localStorage support.
 * Thanks to http://diveintohtml5.info/storage.html
 * @return {bool} True if the browser supports localStorage, false if not.
 */
Chart.prototype.checkLocalStorageSupport = function()
{
    if (!this.localStorageSupport !== null) return this.localStorageSupport;

    try {
        this.localStorageSupport = 'localStorage' in window && window['localStorage'] !== null;
    } catch(e) {
        this.localStorageSupport = false;
    }

    return this.localStorageSupport;
};

Chart.prototype.createControls = function()
{
    var $leftControls  = this.$containerControls.find('.chart-controls-left');
    var $rightControls = this.$containerControls.find('.chart-controls-right');

    // Controls on the left
    var $normalScroller = $('<div></div>');
    $normalScroller.addClass('chart-controls-scroller');
    $normalScroller.addClass('chart-controls-scroller-normal');
    $normalScroller.data('scrollSpeed', -15);

    var $fastScroller = $('<div></div>');
    $fastScroller.addClass('chart-controls-scroller');
    $fastScroller.addClass('chart-controls-scroller-fast');
    $fastScroller.data('scrollSpeed', -40);

    $leftControls.append($normalScroller);
    $leftControls.append($fastScroller);

    // Controls on the right
    var $normalScroller = $('<div></div>');
    $normalScroller.addClass('chart-controls-scroller');
    $normalScroller.addClass('chart-controls-scroller-normal');
    $normalScroller.data('scrollSpeed', 15);

    var $fastScroller = $('<div></div>');
    $fastScroller.addClass('chart-controls-scroller');
    $fastScroller.addClass('chart-controls-scroller-fast');
    $fastScroller.data('scrollSpeed', 40);

    $rightControls.append($normalScroller);
    $rightControls.append($fastScroller);

    // Remove the "regular" scrollbar
    this.$containerScroll.css({
        'overflow-x': 'hidden'
    });
};

Chart.prototype.addBinds = function()
{
    var self = this;

    // Scrolling
    this.$containerControls.find('.chart-controls-scroller').on('mousedown', function() {
        $(this).addClass('chart-controls-scroller-active');
        self.startScroll($(this).data('scrollSpeed'));
    });
    $(document).on('mouseup', function(ev) {
        $('.chart-controls-scroller').removeClass('chart-controls-scroller-active');
        self.stopScroll();
    });

    // Make sure the chart scales correctly on window resize.
    $(window).resize(function() {
        self.updateSizes();
    });

    // Show information about an event when it's clicked on
    this.$chart.find('.chart-event').each(function() {
        $(this).on('click', function() {
            self.showInfo($(this).data('id-event'));
        });
    });
};

Chart.prototype.updateSizes = function()
{
    var chartWidth = this.$chart.width();
    var chartContainerWidth = chartWidth - this.infoWidth - this.infoPadding;

    this.$containerControls.width(chartContainerWidth);
    this.$containerScroll.width(chartContainerWidth);

    this.$info.width(this.infoWidth);
};

Chart.prototype.showInfo = function(id_event)
{
    if (this.events[id_event] === undefined) return;

    var eventData = this.events[id_event];

    // Are we inheriting the group's colour?
    if (eventData.event_colour_inherit == 1)
    {
        console.log(this.groups);
        var groupData = this.groups[eventData.id_group];
        eventData.event_colour = groupData.group_colour;
        eventData.event_colour_rgb = groupData.group_colour_rgb;
    }

    // Heading colours
    var headingColours = {
        'mainHeading': {
            'hex': eventData.colour_hex,
            'rgba': 'rgba('+eventData.colour_rgb+', 0.7)'
        },
        'subHeading': {
            'hex': eventData.colour_hex,
            'rgba': 'rgba('+eventData.colour_rgb+', 0.5)'
        }
    };

    // Heading background styles
    headingColours.mainHeading.style = 'background-color: '+headingColours.mainHeading.hex+';'+
                                       'background-color: '+headingColours.mainHeading.rgba+';';

    headingColours.subHeading.style = 'background-color: '+headingColours.subHeading.hex+';'+
                                      'background-color: '+headingColours.subHeading.rgba+';';

    // Information box container
    var $infoContainer = $('<div></div>');
    $infoContainer.addClass('chart-info-box-container');

    // Event title
    $infoContainer.append('<h1 style="'+headingColours.mainHeading.style+'">'+eventData.event_name+'</h1>');

    /**
     * Start top information table
     */
     console.log(eventData);
    var $infoTable = $('<table></table>');
    $infoTable.attr({
        'cellpadding': '0',
        'cellspacing': '0'
    });
    $infoTable.addClass('chart-info-box-table-info');

    // Date
    var displayDate = {};

    // If we have a pre-defined date to display
    if (eventData.event_start) displayDate.start = eventData.event_start;
    // Or, if we have a properly formatted time (we should)
    else if (eventData.event_time_start)
    {
        var parts = eventData.event_time_start.split('-');
        displayDate.start = parts[2]+'.'+parts[1]+'.'+parts[0];
    }
    // Otherwise fall back to an empty string
    else displayDate.start = '';

    // If we have a pre-defined date to display
    if (eventData.event_end) displayDate.end = eventData.event_end;
    // Or, if we have a properly formatted time (we should)
    else if (eventData.event_time_end)
    {
        var parts = eventData.event_time_end.split('-');
        displayDate.end = parts[2]+'.'+parts[1]+'.'+parts[0];
    }
    // Otherwise fall back to an empty string
    else displayDate.end = '';

    $infoTable.append('<tr>'+
                        '<td class="td-title">Päivämäärä</td>'+
                        '<td class="td-value">'+displayDate.start+' – '+displayDate.end+'</td>'+
                      '</tr>');

    // Location
    if (eventData.event_location)
        $infoTable.append('<tr>'+
                            '<td class="td-title">Sijainti</td>'+
                            '<td class="td-value">'+this.parseMultiLineText(eventData.event_location)+'</td>'+
                          '</tr>');

    // Casusbelli
    if (eventData.event_casusbelli)
        $infoTable.append('<tr>'+
                            '<td class="td-title">Casus belli</td>'+
                            '<td class="td-value">'+this.parseMultiLineText(eventData.event_casusbelli)+'</td>'+
                          '</tr>');

    // Result
    if (eventData.event_result)
        $infoTable.append('<tr>'+
                            '<td class="td-title">Lopputulos</td>'+
                            '<td class="td-value">'+this.parseMultiLineText(eventData.event_result)+'</td>'+
                          '</tr>');

    $infoContainer.append($infoTable);
    /**
     * End top information table
     */
    
    /**
     * Start side information table (e.g. strengths)
     */
    var $sideTable = $('<table></table>');
    $sideTable.attr({
        'cellpadding': '0',
        'cellspacing': '0'
    });
    $sideTable.addClass('chart-info-box-table-side');

    // Sides
    if (eventData.event_side1 || eventData.event_side2)
    {
        $sideTable.append('<tr><td colspan="2"><h2 style="'+headingColours.subHeading.style+'">Osapuolet</h2></td></tr>');
        $sideTable.append('<tr>'+
                            '<td class="td-info">'+this.parseMultiLineText(eventData.event_side1)+'</td>'+
                            '<td class="td-info">'+this.parseMultiLineText(eventData.event_side2)+'</td>'+
                          '</tr>');
    }

    // Strengths
    if (eventData.event_strength1 || eventData.event_strength2)
    {
        $sideTable.append('<tr><td colspan="2"><h2 style="'+headingColours.subHeading.style+'">Vahvuudet</h2></td></tr>');
        $sideTable.append('<tr>'+
                            '<td class="td-info">'+this.parseMultiLineText(eventData.event_strength1)+'</td>'+
                            '<td class="td-info">'+this.parseMultiLineText(eventData.event_strength2)+'</td>'+
                          '</tr>');
    }

    // Strengths
    if (eventData.event_dead1 || eventData.event_dead2 || eventData.event_injured1 || eventData.event_injured2)
    {
        $sideTable.append('<tr><td colspan="2"><h2 style="'+headingColours.subHeading.style+'">Tappiot</h2></td></tr>');
        $sideTable.append('<tr>'+
                            '<td class="td-info">'+
                                '<strong>Kaatuneita:</strong><br>'+
                                this.parseMultiLineText(eventData.event_dead1)+'<br>'+
                                '<strong>Haavoittuneita:</strong><br>'+
                                this.parseMultiLineText(eventData.event_injured1)+
                            '</td>'+
                            '<td class="td-info">'+
                                '<strong>Kaatuneita:</strong><br>'+
                                this.parseMultiLineText(eventData.event_dead2)+'<br>'+
                                '<strong>Haavoittuneita:</strong><br>'+
                                this.parseMultiLineText(eventData.event_injured2)+
                            '</td>'+
                          '</tr>');
    }

    $infoContainer.append($sideTable);
    /**
     * End side information table
     */

    this.$info.hide();
    this.$info.html($infoContainer);
    this.$info.slideDown(200);

    // console.log(this.events[id_event]);
};

/**
 * Parses a multi-line text, changing line breaks to <br>s
 * and turning [fl]ags into their images.
 * @param  {string} text The text to parse
 * @return {string}      The formatted text
 */
Chart.prototype.parseMultiLineText = function(text)
{
    var formattedText = text;

    // Escape HTML
    formattedText = formattedText.replace('<', '&#60;').replace('>', '&#62;');

    // Turn newlines into line breaks
    formattedText = formattedText.replace(/\n/gi, '<br>');

    // Turn bracketed, two-character strings into images for flags
    formattedText = formattedText.replace(
        /\[([A-Za-z_-]{2,8})\]/gi,
        '<div class="flag flag-$1"></div>'
    );

    // Return the formatted text
    return formattedText;
};

Chart.prototype.startScroll = function(scrollSpeed)
{
    this.scrollSpeed = scrollSpeed;
    this.scrollLoop();
};

Chart.prototype.scrollLoop = function()
{
    if (this.scrollSpeed == 0) return;

    this.$containerScroll.scrollLeft(this.$containerScroll.scrollLeft() + this.scrollSpeed);

    var self = this;
    setTimeout(function() {
        self.scrollLoop();
    }, 16);
};

Chart.prototype.stopScroll = function()
{
    this.scrollSpeed = 0;
    if (this.checkLocalStorageSupport) localStorage.setItem('scrollState', this.$containerScroll.scrollLeft());
};